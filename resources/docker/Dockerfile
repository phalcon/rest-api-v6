ARG PHP_VERSION=8.4

FROM php:${PHP_VERSION}-fpm

ARG UID=1000
ARG GID=1000
ARG USER=phalcon
ARG GROUP=phalcon

# hadolint ignore=DL3022
COPY --from=ghcr.io/mlocati/php-extension-installer \
        /usr/bin/install-php-extensions \
        /usr/local/bin/

# This is the folder structure from where compose is run from (root)
COPY resources/docker/config/ /config/

SHELL [ "/bin/bash", "-o", "pipefail", "-c" ]

RUN set -eux \
# Add user and group \
    && getent group "${GROUP}" || groupadd -g "${GID}" "${GROUP}" \
    && id -u "${USER}" &>/dev/null || useradd -l -u "${UID}" -g "${GID}" -d /app "${USER}" \
    && usermod -s /bin/bash "${USER}" \
    && mkdir -p /app /app/public /app/storage /run/nginx /run/supervisor \
    && mv /config/public/index.php /app/public/index.php \
    && chown "${USER}":"${GROUP}" /app \
    && chmod 0770 /app \
    && apt update \
# Install applications \
    && apt install --no-install-recommends --no-install-suggests -q -y \
      apt-utils \
      git \
      nano \
      nginx \
      ssh \
      supervisor \
      unzip \
      zip \
# Install base extensions \
    && install-php-extensions \
      igbinary \
      pcov \
      pdo_mysql \
      redis \
      xdebug \
      xsl \
      zip \
# Configure PHP-FPM and PHP \
    && mv /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini \
    && mv /config/php/fpm-pool.conf /usr/local/etc/php-fpm.d/www.conf \
    && mv /config/php/php.ini /usr/local/etc/php/conf.d/80-custom.ini \
    && sed -i -e "s/group = www-data/group = ${GROUP}/" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i -e "s/listen.group = www-data/listen.group = ${GROUP}/" /usr/local/etc/php-fpm.d/www.conf \
# Configure  nginx \
    && rm -f /etc/nginx/sites-enabled/default \
    && mv /config/nginx/nginx.conf /etc/nginx/nginx.conf \
    && mv /config/nginx/default.conf /etc/nginx/conf.d/default.conf \
    && sed -i -e "s/user             nobody nobody;/user             www-data ${GROUP};/" /etc/nginx/nginx.conf \
# Configure supervisord \
    && mv /config/supervisor/supervisor.conf /etc/supervisor/supervisord.conf \
# Configure bashrc and permissions \
    && echo "" >> /etc/bash.bashrc \
    && cat /config/bashrc >> /etc/bash.bashrc \
    && rm -fR /config \
    && chown -R ${USER}:${GROUP} /app \
    && chgrp -R ${GROUP} /var/log/nginx /run/nginx /run/supervisor \
    && chmod -R 0775 /app /var/log/nginx /run/nginx /run/supervisor \
# Cleanup \
   && apt autoremove --purge -y \
   && apt autoclean -y \
   && apt clean -y \
   && rm -rf /tmp/* /var/tmp/* \
   && find /var/cache/apt/archives /var/lib/apt/lists -not -name lock -type f -delete \
   && find /var/cache -type f -delete \
   && find /var/log -type f -delete

# hadolint ignore=DL3022
COPY --from=composer/composer:2 \
        --chown=${USER}:${GROUP} \
        --chmod=0770 \
        /usr/bin/composer \
        /usr/local/bin/composer

# Configure a healthcheck to validate that everything is up&running
HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:80/fpm-ping || exit 1

WORKDIR /app

USER ${USER}

EXPOSE 80

# Let supervisord start nginx & php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
