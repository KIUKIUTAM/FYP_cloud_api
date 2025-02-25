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
