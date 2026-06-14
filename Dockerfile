# ============================================================
# FinControl — Dockerfile
# Imagem PHP 8.3 + Apache + Extensões Laravel
# ============================================================
FROM php:8.3-apache

# Argumentos de build
ARG USER_ID=1000
ARG GROUP_ID=1000

# Variáveis de ambiente
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Instalar dependências do sistema e extensões PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        curl \
        unzip \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libzip-dev \
        libonig-dev \
        libxml2-dev \
        default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        gd \
        zip \
        xml \
        bcmath \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configurar Apache: apontar DocumentRoot para /public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf \
    && a2enmod rewrite headers

# Configurar PHP para produção/desenvolvimento
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Criar usuário não-root para permissões corretas
RUN groupadd --gid $GROUP_ID appuser \
    && useradd --uid $USER_ID --gid $GROUP_ID --create-home appuser

# Diretório de trabalho
WORKDIR /var/www/html

# Copiar composer.json e lock primeiro (cache de dependências)
COPY composer.json composer.lock* ./

# Instalar dependências (sem scripts para evitar erros antes de copiar o código)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copiar todo o código do projeto
COPY . .

# Gerar autoload otimizado e rodar scripts
RUN composer dump-autoload --optimize \
    && php artisan package:discover --ansi || true

# Criar diretórios necessários e definir permissões
RUN mkdir -p storage/logs \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/app/public \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Expor porta 80 (Apache)
EXPOSE 80

# Script de inicialização
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
 