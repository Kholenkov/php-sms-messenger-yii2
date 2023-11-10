FROM nginx:1.21-alpine

COPY ./docker/nginx/config/default.conf /etc/nginx/conf.d/default.conf
COPY ./app/src/index.php /app/src/index.php

WORKDIR /app