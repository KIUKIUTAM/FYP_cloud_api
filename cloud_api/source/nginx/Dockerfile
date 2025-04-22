FROM node:lts as build

WORKDIR /app

COPY ../vue_front_page/ .

RUN npm cache clean --force && \
    npm install --include=optional && \
    npm run build

RUN npm install

RUN npm run build

FROM nginx:stable as stage

COPY nginx/nginx.conf /etc/nginx/nginx.conf
COPY --from=build /app/dist /home/vue_server/

RUN ln -sf /dev/stdout /var/log/nginx/access.log && ln -sf /dev/stderr /var/log/nginx/error.log

EXPOSE 8088
STOPSIGNAL SIGTERM
CMD ["nginx","-g","daemon off;"]
