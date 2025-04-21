cd E:\dji_project\cloud_api_sample_v1\cloud_api_sample
docker load -i cloud_api_sample_docker_v1.10.0.tar


cd source/backend_service/
docker build -t dji/cloud_api_sample:latest .

cd ..
cd ..
cd source/nginx/
docker build -t dji/nginx:latest .


docker compose up -d