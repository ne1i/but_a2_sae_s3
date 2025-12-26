FROM php:8.4-alpine

COPY ./src /app/src
RUN mkdir /data

VOLUME [ "/data" ]

EXPOSE 8080
ENV DB_PATH=/data/fage.db
ENV DEBUG=false

CMD [ "php", "-S", "0.0.0.0:8080", "-t", "/app/src/public" ]
