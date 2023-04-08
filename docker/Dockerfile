FROM php:7.3-apache

RUN apt-get update && \
apt-get install apt-utils -yqq --allow-unauthenticated && \
apt-get install wget bzip2 curl libicu-dev libncurses5-dev libncurses5 -yqq --allow-unauthenticated


RUN apt-get install git imagemagick mcrypt libcurl4-gnutls-dev libmcrypt-dev libvpx-dev libjpeg-dev libc-client-dev libkrb5-dev libpng-dev libxpm-dev zlib1g-dev libfreetype6-dev libxml2-dev libexpat1-dev libbz2-dev libgmp3-dev libldap2-dev unixodbc-dev libpq-dev libsqlite3-dev libaspell-dev libsnmp-dev libpcre3-dev libtidy-dev libmagickwand-dev libzip-dev -yqq --allow-unauthenticated
RUN apt-get install -y --no-install-recommends firebird-dev
RUN docker-php-ext-install curl json intl gd xml zip bz2 opcache  
RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl
RUN docker-php-ext-install imap
RUN docker-php-ext-install interbase \
	&& docker-php-ext-enable interbase
RUN docker-php-ext-install pdo_firebird


