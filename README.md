download:https://drive.google.com/file/d/1cR6iPrhp4sGTpd3Qy7RPBynmUc7pq-Hg/view?usp=drive_link (cloud_api_sample_docker_v1.10.0.tar)

1.backend
edit "cloud_api_sample/source/backend_service/sample/src/main/resources/application.yml"<br>
change the ip to your localhost<br>

2.<br>
edit cloud_api_sample/source/nginx/front_page/src/api/http/config.ts<br>
change the ip to your localhost<br>

3.<br>
put the model in to FYP_cloud_api\cloud_api\source\python\app\model <br>
https://drive.google.com/file/d/1ykycizTj5vJ-9QyvQrGH4miwfctzC_X1/view?usp=sharing

minio config:
*change credential key
*bucket name: crack-detection
(set public)
docker-compose\php\src\fyp_frontend\dashboard\_main\minio_config.inc.php
docker-compose\python\app\app.py
docker-compose\php\src\fyp_frontend\dashboard\_main\images_upload.inc.php


open docker :<br>



docker compose build <br>
docker compose up -d
<br>


O:\cloud_api_version\cloud_api_version\dji_project\cloud_api_sample_v2\cloud_api_sample\source\nginx\front_page
npm run service


OSS(minIO):<br>
MINIO_ROOT_USER: ROOTUSER <br>
MINIO_ROOT_PASSWORD: CHANGEME123<br>
http://127.0.0.1:9000<br>

window<br>
docker run -p 9000:9000 -p 9001:9001 --name minio1 -v D:\minio\data:/data -e "MINIO_ROOT_USER=ROOTUSER" -e "MINIO_ROOT_PASSWORD=CHANGEME123" ` quay.io/minio/minio server /data --console-address ":9001"<br>

MAC:<br>
docker run \ -p 9000:9000 \ -p 9001:9001 \ --name minio1 \ -v /Users/yourusername/minio/data:/data \ -e "MINIO_ROOT_USER=ROOTUSER" \ -e "MINIO_ROOT_PASSWORD=CHANGEME123" \ quay.io/minio/minio server /data --console-address ":9001"<br>
