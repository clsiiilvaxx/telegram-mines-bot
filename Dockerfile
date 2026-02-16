FROM php:8.2-cli

WORKDIR /app
COPY . .

RUN apt-get update && apt-get install -y \
    libpng-dev \
    && docker-php-ext-install gd

EXPOSE 10000

CMD php -S 0.0.0.0:10000 & php bot.php
