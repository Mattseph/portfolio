FROM webdevops/php-nginx:8.3

RUN apt-get update -y \
    && curl -sLS https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm \
    && apt-get update \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*


RUN npm install -g yarn

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


# ENV APP_ENV production

WORKDIR /app

COPY --chown=application:application . .

# Set ownership and executable permissions as root
RUN chown -R application:application /app \
    && chmod +x /app/entrypoint.sh

USER application

# Disable artisan auto-scripts temporarily
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# Run artisan package discovery explicitly without DB calls
RUN php artisan package:discover --ansi --no-interaction || true

# Don't run DB-dependent commands here!
# ENTRYPOINT ["/app/entrypoint.sh"]