download:https://drive.google.com/file/d/1cR6iPrhp4sGTpd3Qy7RPBynmUc7pq-Hg/view?usp=drive_link (cloud_api_sample_docker_v1.10.0.tar)

1.backend
edit "cloud_api_sample/source/backend_service/sample/src/main/resources/application.yml"
change the ip to your localhost

2.
edit cloud_api_sample/source/nginx/front_page/src/api/http/config.ts
change the ip to your localhost

open docker :
docker load -i cloud_api_sample_docker_v1.10.0.tar

remove nginx


cd E:\dji_project\cloud_api_sample_v1\cloud_api_sample
<br>
docker load -i cloud_api_sample_docker_v1.10.0.tar
<br>

cd source/backend_service/
<br>

docker build -t dji/cloud_api_sample:latest .
<br>

cd ..
<br>

cd ..
<br>

cd source/nginx/
<br>

docker build -t dji/nginx:latest .
<br>


docker compose up -d
<br>





OSS(minIO):
MINIO_ROOT_USER: ROOTUSER MINIO_ROOT_PASSWORD: CHANGEME123 http://127.0.0.1:9000

window
docker run -p 9000:9000 -p 9001:9001 --name minio1 -v D:\minio\data:/data -e "MINIO_ROOT_USER=ROOTUSER" -e "MINIO_ROOT_PASSWORD=CHANGEME123" ` quay.io/minio/minio server /data --console-address ":9001"

MAC:
docker run \ -p 9000:9000 \ -p 9001:9001 \ --name minio1 \ -v /Users/yourusername/minio/data:/data \ -e "MINIO_ROOT_USER=ROOTUSER" \ -e "MINIO_ROOT_PASSWORD=CHANGEME123" \ quay.io/minio/minio server /data --console-address ":9001"
