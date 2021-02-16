FROM php:7.2-cli-buster as builder

WORKDIR /app
COPY . /app

RUN apt-get update && \
    apt-get install -y \
        zip \
        curl \
        git && \
    curl -L getcomposer.org/installer | php -- --filename=composer && \
    chmod +x ./composer && \
    ./composer update && \
    rm ./composer

FROM php:7.2-alpine

WORKDIR /app
COPY --from=builder /app /app

ENTRYPOINT [ "/app/fixer" ]
