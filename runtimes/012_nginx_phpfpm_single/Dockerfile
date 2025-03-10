FROM php:8.3-fpm

RUN set -xe; \
    apt update; \
    apt install -y unzip nginx supervisor

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
        exif \
        intl \
        opcache \
        pdo_pgsql \
        tidy \
        gd \
        bcmath \
        sockets \
        zip && \
    install-php-extensions @composer;

COPY "./project" "/var/www/symfony"

WORKDIR /var/www/symfony

RUN cp .env.example .env.local

RUN rm -rf vendor && \
    composer install --no-dev --no-scripts --prefer-dist --no-interaction && \
    composer dump-autoload --no-dev --classmap-authoritative && \
    composer check-platform-reqs && \
    php bin/console cache:clear && \
    php bin/console cache:warmup

COPY ./runtimes/012_nginx_phpfpm_single/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./runtimes/012_nginx_phpfpm_single/nginx/conf.d/symfony7site.conf /etc/nginx/conf.d/symfony7site.conf
COPY ./runtimes/012_nginx_phpfpm_single/supervisord.conf /etc/supervisord.conf

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini;
COPY ./runtimes/012_nginx_phpfpm_single/fpm/php.ini /usr/local/etc/php/conf.d/custom-php.ini
COPY ./runtimes/012_nginx_phpfpm_single/fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

CMD ["supervisord", "-c", "/etc/supervisord.conf"]
