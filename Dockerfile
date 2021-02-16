FROM dezar/php-cli:latest as builder

COPY . /app

RUN ./composer update && \
    rm ./composer

FROM php:7.2-alpine

WORKDIR /app
COPY --from=builder /app /app

ENTRYPOINT [ "/app/fixer" ]
