# syntax=docker/dockerfile:1
ARG PHP_VERSION=8.5

FROM php:${PHP_VERSION}-fpm

ARG UID=1000
ARG GID=1000
ARG USER=phalcon
ARG GROUP=phalcon

# hadolint ignore=DL3022
COPY --from=ghcr.io/mlocati/php-extension-installer \
        /usr/bin/install-php-extensions \
        /usr/local/bin/

SHELL [ "/bin/bash", "-o", "pipefail", "-c" ]

RUN set -eux \
# Add user and group \
    && getent group "${GROUP}" || groupadd -g "${GID}" "${GROUP}" \
    && id -u "${USER}" &>/dev/null || useradd -l -u "${UID}" -g "${GID}" -d /app "${USER}" \
    && usermod -s /bin/bash "${USER}" \
    && mkdir -p /app /app/public /app/storage /run/php \
    && chown "${USER}":"${GROUP}" /app /run/php \
    && apt-get update \
    && apt-get install --no-install-recommends --no-install-suggests -q -y \
      git \
      unzip \
      zip \
# pcov provides coverage. Xdebug is deliberately not installed. \
    && install-php-extensions \
      igbinary \
      opcache \
      pcov \
      pdo_mysql \
      redis \
      zip \
# Cleanup \
    && apt-get autoremove --purge -y \
    && apt-get clean -y \
    && rm -rf /var/lib/apt/lists/* /var/tmp/* \
    && find /var/cache -type f -delete

COPY resources/docker/php/php.ini       /usr/local/etc/php/conf.d/80-custom.ini
COPY resources/docker/php/fpm-pool.conf /usr/local/etc/php-fpm.d/zz-www.conf
COPY resources/docker/.bashrc           /home/${USER}/.bashrc

# hadolint ignore=DL3022
COPY --from=composer/composer:2 \
        --chown=${USER}:${GROUP} \
        /usr/bin/composer \
        /usr/local/bin/composer

ENV HOME=/app \
    COMPOSER_HOME=/app/.composer

WORKDIR /app

USER ${USER}

EXPOSE 9000

CMD ["php-fpm", "-F"]
