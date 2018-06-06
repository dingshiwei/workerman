FROM php:7.1

MAINTAINER geek <dingshiwei@corp-ci.com>

ENV COMPOSER_ALLOW_SUPERUSER 1

ENV DEBIAN_FRONTEND noninteractive

RUN echo 'ulimit -S -c 0 > /dev/null 2>&1' >> /root/.bashrc

RUN /bin/bash -c "source /root/.bashrc"

RUN /bin/cp /usr/share/zoneinfo/Asia/Shanghai /etc/localtime \
    && echo 'Asia/Shanghai' > /etc/timezone

RUN apt-get update \
    && apt-get install -y \
        curl \
        wget \
        git \
        zip \
        libz-dev \
        libssl-dev \
        libnghttp2-dev \
    && apt-get clean \
    && apt-get autoremove

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && composer self-update --clean-backups

RUN docker-php-ext-install pdo_mysql

RUN docker-php-ext-install mysqli

RUN mkdir -p /usr/src/php/ext/redis \
    && curl -L https://github.com/phpredis/phpredis/archive/3.1.4.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
    && echo 'redis' >> /usr/src/php-available-exts \
    && docker-php-ext-install redis

RUN wget https://github.com/redis/hiredis/archive/v0.13.3.tar.gz -O hiredis.tar.gz \
    && mkdir -p hiredis \
    && tar -xf hiredis.tar.gz -C hiredis --strip-components=1 \
    && rm hiredis.tar.gz \
    && ( \
        cd hiredis \
        && make -j$(nproc) \
        && make install \
        && ldconfig \
    ) \
    && rm -r hiredis

WORKDIR /opt/dsw/workerman

ARG CACHEBUST=1

RUN git clone https://github.com/dingshiwei/workerman.git  /opt/dsw/workerman

RUN composer install --no-plugins --no-progress \
    && composer dump-autoload -o 

EXPOSE 80
