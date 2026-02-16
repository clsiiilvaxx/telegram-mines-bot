FROM php:8.2-cli

WORKDIR /app

COPY . .

RUN apt-get update && apt-get install -y \
    libpng-dev \
    && docker-php-ext-install gd

CMD ["php", "bot.php"]
