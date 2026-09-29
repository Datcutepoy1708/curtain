FROM php:8.2-cli-alpine

# Cài đặt extension cần thiết cho Laravel và MySQL
RUN docker-php-ext-install pdo pdo_mysql bcmath

# Lấy Composer từ image chính thức
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Cài đặt thư viện Laravel
RUN composer install --no-dev --optimize-autoloader --no-interaction

EXPOSE 8000

CMD ["sh", "-c", "php artisan key:generate --force && php artisan serve --host=0.0.0.0 --port=8000"]
