FROM php:8.4-alpine

COPY ./src /app/src
COPY ./public /app/public
RUN mkdir /data

VOLUME [ "/data" ]

EXPOSE 8080

CMD [ "php", "-S", "0.0.0.0:8080", "-t", "/app/public" ]
