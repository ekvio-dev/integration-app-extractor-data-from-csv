FROM php:7.4.2-cli-alpine

LABEL Description="Csv file users extractor"

ENV BuildTimezone Europe/Moscow

RUN mkdir /app
COPY . /app
WORKDIR /app
RUN \
# add system dependencies required for build
    apk add --no-cache --virtual build-deps \
        autoconf \
        gcc \
        make \
        g++ \
        libssh2-dev \
        php7-mbstring && \
# download and install composer
    curl -s -f -L -o /tmp/composer-setup.php https://getcomposer.org/installer && \
    EXPECTED_CHECKSUM="$(wget -q -O - https://composer.github.io/installer.sig)" && \
    ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', '/tmp/composer-setup.php');")" && \
    if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then >&2 echo 'ERROR: Invalid installer checksum' && \
    rm /tmp/composer-setup.php && \
    exit 1; fi && \
    php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer && \
    composer --ansi --version --no-interaction && \
    rm -f /tmp/*.php && \
# install system persistent dependencies
    apk add --no-cache --virtual persistent-deps \
        libssh2 \
        shadow \
        tzdata && \
    useradd --create-home --uid 1200 --user-group --shell /bin/sh app && \
    chown -R app:app /app && \
    cp /usr/share/zoneinfo/${BuildTimezone} /etc/localtime && \
    echo ${BuildTimezone} > /etc/timezone && \
    mkfifo /tmp/stdout && \
    chmod 777 /tmp/stdout && \
# create custom php config
    cp .ops/conf/php/custom.ini /usr/local/etc/php/conf.d/custom.ini && \
# install composer dependencies
    composer install --prefer-dist && \
    composer clearcache && \
# remove system dependencies required for build
    apk del build-deps && \
# remove files that not required to run application
    rm -rf .ops \
           composer.json \
           composer.lock \
           Dockerfile* \
           .dockerignore

WORKDIR /app
