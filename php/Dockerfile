# PHP 8 FPMの公式イメージをベースにする
FROM php:8-fpm

# システムパッケージの更新と必要なパッケージのインストール
RUN apt-get update && apt-get install -y \
    libpq-dev \                        
    git \                              
    curl \                             
    unzip \       
    iputils-ping \
    gcc \
    make \
    vim \
    libjpeg-dev \
    libfreetype6-dev \
    libmcrypt-dev \
    gnupg \
    openssl \
    nodejs \                     
    npm \                        
    && docker-php-ext-install pdo pdo_pgsql \ 
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Xdebugのインストール(xdebugの2重ロード防止のためdocker-php-ext-xdebug.iniを削除)
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && rm /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# 作業ディレクトリを設定
WORKDIR /var/www/work

# パーミッションの設定
RUN chown -R www-data:www-data /var/www/work
