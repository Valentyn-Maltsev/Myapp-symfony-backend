FROM php:7.4-fpm

# DB #
ARG DB_HOST=""
ARG DB_PORT=""
ARG DB_NAME=""
ARG DB_USER=""
ARG DB_PASSWORD=""

ENV DB_HOST=${DB_HOST}
ENV DB_PORT=${DB_PORT}
ENV DB_NAME=${DB_NAME}
ENV DB_USER=${DB_USER}
ENV DB_PASSWORD=${DB_PASSWORD}
RUN echo ${DB_HOST}

RUN apt-get update && apt-get install -y \
    libpq-dev \
    wget \
    zlib1g-dev \
    libmcrypt-dev \
    libzip-dev

RUN docker-php-ext-install pdo pdo_mysql

RUN wget https://getcomposer.org/installer -O - -q | php -- --install-dir=/bin --filename=composer --quiet

RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git

WORKDIR /app