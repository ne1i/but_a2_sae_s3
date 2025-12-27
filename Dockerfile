FROM php:8.5-alpine

COPY ./src /app/src
COPY ./vendor /app/vendor
RUN mkdir /data

VOLUME [ "/data" ]

EXPOSE 8080
ENV DB_PATH=/data/fage.db
ENV DEBUG=false

CMD [ "php", "-S", "0.0.0.0:8080", "-t", "/app/src/public" ]
