# Stage 1: Build environment
FROM python:3.11-slim as builder

WORKDIR /app

# Install build dependencies only
RUN apt-get update && apt-get install -y \
    build-essential \
    libjpeg-dev \
    zlib1g-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Python dependencies with CPU-only PyTorch
COPY requirements.txt .
RUN pip install --user --no-cache-dir \
    torch==2.3.0+cpu --extra-index-url https://download.pytorch.org/whl/cpu \
    -r requirements.txt

# Stage 2: Runtime environment
FROM python:3.11-slim-bullseye

WORKDIR /app

# Create model directory explicitly
RUN mkdir -p /app/model

# Copy only necessary files from builder
COPY --from=builder /root/.local /root/.local

# Copy app code and model files
COPY . /app

# Copy model files explicitly
COPY app/model/YOLOv11x-Crack.pt /app/model/
COPY app/model/yolov8n.pt /app/model/

# Install runtime dependencies
RUN apt-get update && apt-get install -y \
    libjpeg62-turbo \
    zlib1g \
    libgl1 \
    libglib2.0-0 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Set proper permissions
RUN chmod -R 755 /app/model

# Ensure Python user-installed packages are in PATH
ENV PATH=/root/.local/bin:$PATH

# Environment variables
ENV FLASK_APP=app.py \
    FLASK_DEBUG=0 \
    PYTHONUNBUFFERED=1

CMD ["flask", "run", "--host=0.0.0.0", "--port=8000"]