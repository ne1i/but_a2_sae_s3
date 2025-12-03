FROM php:8.4-alpine

RUN apk add --no-cache sqlite sqlite-dev && docker-php-ext-install pdo_sqlite sqlite3

COPY ./src /app/src
COPY ./public /app/public

VOLUME [ "/data" ]

EXPOSE 8080

CMD [ "php", "-S", "0.0.0.0:8080", "-t", "/app/public" ]
