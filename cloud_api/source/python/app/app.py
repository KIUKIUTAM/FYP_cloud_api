from flask import Flask, request, jsonify
import os
import io
import uuid
import logging
from PIL import Image
from ultralytics import YOLO
from minio import Minio
import traceback

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = Flask(__name__)
app.config['MAX_CONTENT_LENGTH'] = 50 * 1024 * 1024

# Allowed file extensions
ALLOWED_EXTENSIONS = {'png', 'jpg', 'jpeg', 'gif'}

def allowed_file(filename):
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS

# Model paths - using absolute paths
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
MODEL_DIR = os.path.join(SCRIPT_DIR, 'model')

# Ensure model directory exists
os.makedirs(MODEL_DIR, exist_ok=True)

MODELS = {
    'crack': {
        'path': os.path.join(MODEL_DIR, 'YOLOv11x-Crack.pt'),
        'conf': 0.4,
        'iou': 0.5
    },
    'car': {
        'path': os.path.join(MODEL_DIR, 'yolov8n.pt'),
        'conf': 0.3,  
        'iou': 0.6,
        'classes': [2, 7]  # COCO classes: 2 (car), 7 (truck)
    }
}

# Initialize models
models = {}
for detection_type, config in MODELS.items():
    try:
        if not os.path.exists(config['path']):
            logger.error(f"Model file not found: {config['path']}")
            logger.error(f"Current directory: {os.getcwd()}")
            logger.error(f"Model directory contents: {os.listdir(os.path.dirname(config['path']))}")
            continue
            
        logger.info(f"Loading model for {detection_type} from {config['path']}")
        model = YOLO(config['path'])
        models[detection_type] = model
        logger.info(f"Successfully loaded {detection_type} model")
    except Exception as e:
        logger.error(f"ERROR loading {detection_type} model: {str(e)}")
        logger.error(f"Model path: {config['path']}")
        logger.error(f"Full error: {repr(e)}")
        logger.error(f"Stack trace: {traceback.format_exc()}")

# Check which models were successfully loaded
for detection_type in MODELS.keys():
    if detection_type not in models:
        logger.error(f"Failed to load {detection_type} model")

if not models:
    logger.error("No models were successfully loaded! Application may not function correctly.")
    exit(1)

# Minio setup
minio_client = Minio(
    "minio:9000",
    access_key="Sf6eJNH2UvUyjugpYvws",
    secret_key="V4WX6EHEFIQyna5HyP0Dv3hIoPu4VIn6zLeHmqQd",
    secure=False
)

# Bucket name - assuming already created
BUCKET_NAME = "crack-detection"

@app.route('/upload', methods=['POST'])
def upload_file():
    try:
        # Check requirements
        if not (mission_id := request.form.get('mission_id')):
            logger.error("Mission ID not provided")
            return jsonify({'error': 'Mission ID not provided'}), 400

        if 'file' not in request.files:
            logger.error("No file part in request")
            return jsonify({'error': 'No file part'}), 400
            
        file = request.files['file']
        
        if file.filename == '':
            logger.error("No selected file")
            return jsonify({'error': 'No selected file'}), 400

        if not allowed_file(file.filename):
            logger.error(f"Invalid file type: {file.filename}")
            return jsonify({'error': 'Invalid file type. Allowed types: png, jpg, jpeg, gif'}), 400

        # Get detection type
        detection_type = request.form.get('detection_type', 'crack')
        
        if detection_type not in models:
            logger.error(f"Invalid detection type: {detection_type}")
            return jsonify({'error': f'Invalid detection type: {detection_type}'}), 400

        # File path setup
        file_ext = os.path.splitext(file.filename)[1].lower().lstrip('.')
        if not file_ext:  # Handle files without extension
            file_ext = 'jpg'
        
        filename = f"{uuid.uuid4().hex}.{file_ext}"
        original_object = f"mission_{mission_id}/original/{filename}"
        processed_object = f"mission_{mission_id}/processed/{filename}"

        # Storage check
        if not minio_client:
            logger.error("Storage service not available")
            return jsonify({'error': 'Storage service not available'}), 500

        # Upload original to Minio
        try:
            file.seek(0)
            content_type = f'image/{file_ext}' if file_ext != 'jpg' else 'image/jpeg'
            minio_client.put_object(
                BUCKET_NAME,
                original_object,
                file,
                length=-1,
                part_size=10*1024*1024,
                content_type=content_type
            )
        except Exception as e:
            logger.error(f"Failed to upload to storage: {str(e)}")
            return jsonify({'error': f"Failed to upload to storage: {str(e)}"}), 500

        # Initialize values
        detections = 0
        processing_successful = False
        
        # Get model configuration
        model_config = MODELS[detection_type]
        model = models[detection_type]
        
        # Process image if model is available
        if model:
            try:
                file.seek(0)
                img = Image.open(file.stream)
                
                # Use different parameters based on detection type
                if detection_type == 'car':
                    results = model(img, conf=model_config['conf'], iou=model_config['iou'], classes=model_config['classes'])
                else:
                    results = model(img, conf=model_config['conf'], iou=model_config['iou'])
                
                # Save processed image to Minio
                if results:
                    # Get number of detections
                    detections = len(results[0].boxes)
                    
                    # If car detection, show class IDs and verify classes
                    if detection_type == 'car' and detections > 0:
                        # Get class IDs
                        class_ids = results[0].boxes.cls.tolist()
                        
                        # Verify all detections are cars or trucks (class 2 or 7)
                        valid_detections = [c for c in class_ids if int(c) in model_config['classes']]
                        
                        # Override detections count to only include valid detections
                        detections = len(valid_detections)
                    
                    # Only process and save the image if valid detections were found
                    if detections > 0:
                        processed_img = results[0].plot()
                        img_bytes = io.BytesIO()
                        Image.fromarray(processed_img).save(img_bytes, format='JPEG')
                        img_bytes.seek(0)

                        minio_client.put_object(
                            BUCKET_NAME,
                            processed_object,
                            img_bytes,
                            length=img_bytes.getbuffer().nbytes,
                            content_type='image/jpeg'
                        )
                        processing_successful = True
                    else:
                        # No detections found - copy the original image to the processed path
                        try:
                            response = minio_client.get_object(BUCKET_NAME, original_object)
                            img_bytes = response.read()
                            minio_client.put_object(
                                BUCKET_NAME,
                                processed_object,
                                io.BytesIO(img_bytes),
                                length=len(img_bytes),
                                content_type=f'image/{file_ext}' if file_ext != 'jpg' else 'image/jpeg'
                            )
                            processing_successful = True
                        except Exception as e:
                            logger.error(f"Failed to copy original image: {str(e)}")
                            processing_successful = False
            except Exception as e:
                logger.error(f"Image processing failed: {str(e)}")
                return jsonify({
                    'filename': filename,
                    'original_path': original_object,
                    'processed_path': None,
                    'detections': 0,
                    'warning': f"Image processing failed: {str(e)}"
                })

        # Return success response
        response_data = {
            'filename': filename,
            'detections': detections,
            'original_path': original_object,
            'processed_path': processed_object if processing_successful else None
        }
        return jsonify(response_data)
        
    except Exception as e:
        logger.error(f"Unexpected error: {str(e)}")
        return jsonify({'error': f"Unexpected error: {str(e)}"}), 500

@app.route('/auto-upload', methods=['POST'])
def auto_upload():
    try:
        # Get data from form (works with both form-urlencoded and multipart)
        data = request.form if request.form else request.get_json()
        
        if not data:
            logger.error("No data received")
            return jsonify({'error': 'No data received'}), 400

        if not (mission_id := data.get('mission_id')):
            logger.error("Mission ID not provided")
            return jsonify({'error': 'Mission ID not provided'}), 400

        if not (file_name := data.get('file_name')):
            logger.error("File name not provided")
            return jsonify({'error': 'File name not provided'}), 400

        # Get detection type
        detection_type = request.form.get('model', 'crack')
        
        if detection_type not in models:
            logger.error(f"Invalid detection type: {detection_type}")
            return jsonify({'error': f'Invalid detection type: {detection_type}'}), 400

        # Get model configuration
        model_config = MODELS[detection_type]
        model = models[detection_type]

        # File path setup
        file_ext = os.path.splitext(file_name)[1].lower().lstrip('.')
        if not file_ext:  # Handle files without extension
            file_ext = 'jpg'

        # Setup Minio paths
        source_object = f"drone-auto-upload/{file_name}"
        original_object = f"mission_{mission_id}/original/{file_name}"
        processed_object = f"mission_{mission_id}/processed/{file_name}"

        # Storage check
        if not minio_client:
            logger.error("Storage service not available")
            return jsonify({'error': 'Storage service not available'}), 500

        # Initialize values
        processing_successful = False
        detections = 0

        try:
            # Get the image from source location in Minio
            response = minio_client.get_object(BUCKET_NAME, source_object)
            img_data = response.read()
            
            # Save to mission's original folder
            content_type = f'image/{file_ext}' if file_ext != 'jpg' else 'image/jpeg'
            minio_client.put_object(
                BUCKET_NAME,
                original_object,
                io.BytesIO(img_data),
                length=len(img_data),
                content_type=content_type
            )

            # Process the image
            if model:
                try:
                    img = Image.open(io.BytesIO(img_data))
                    
                    # Use different parameters based on detection type
                    if detection_type == 'car':
                        results = model(img, conf=model_config['conf'], iou=model_config['iou'], classes=model_config['classes'])
                    else:
                        results = model(img, conf=model_config['conf'], iou=model_config['iou'])
                    
                    if results:
                        # Get number of detections
                        detections = len(results[0].boxes)
                        
                        # If car detection, verify classes
                        if detection_type == 'car' and detections > 0:
                            class_ids = results[0].boxes.cls.tolist()
                            valid_detections = [c for c in class_ids if int(c) in model_config['classes']]
                            detections = len(valid_detections)
                        
                        # Process and save the image if valid detections were found
                        if detections > 0:
                            processed_img = results[0].plot()
                            img_bytes = io.BytesIO()
                            Image.fromarray(processed_img).save(img_bytes, format='JPEG')
                            img_bytes.seek(0)

                            minio_client.put_object(
                                BUCKET_NAME,
                                processed_object,
                                img_bytes,
                                length=img_bytes.getbuffer().nbytes,
                                content_type='image/jpeg'
                            )
                            processing_successful = True
                        else:
                            # No detections - copy original to processed
                            minio_client.put_object(
                                BUCKET_NAME,
                                processed_object,
                                io.BytesIO(img_data),
                                length=len(img_data),
                                content_type=content_type
                            )
                            processing_successful = True

                except Exception as e:
                    logger.error(f"Image processing failed: {str(e)}")
                    return jsonify({
                        'filename': file_name,
                        'original_path': original_object,
                        'processed_path': None,
                        'detections': 0,
                        'warning': f"Image processing failed: {str(e)}"
                    })

        except Exception as e:
            logger.error(f"Failed to process from storage: {str(e)}")
            return jsonify({'error': f"Failed to process from storage: {str(e)}"}), 500

        # Return success response
        response_data = {
            'filename': file_name,
            'detections': detections,
            'original_path': original_object,
            'processed_path': processed_object if processing_successful else None
        }
        return jsonify(response_data)
        
    except Exception as e:
        logger.error(f"Unexpected error: {str(e)}")
        return jsonify({'error': f"Unexpected error: {str(e)}"}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8000, debug=True)  # Enable debug mode for more information
