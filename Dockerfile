FROM php:7.4-fpm
LABEL Name=teamplanning Version=0.0.1 Author=cedrix73
WORKDIR  /var/www 
ENV TEAM_DATABASE_SERVER localhost
ENV TEAM_DATABASE_USER root
ENV TEAM_DATABASE_PASSWORD cedrix
ENV TEAM_DATABASE_NAME team_planning 
ENV DB_CONNECTION_MODE docker
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \ 
    libpq-dev \ 
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
# RUN apt-get update
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd 
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli 
RUN docker-php-ext-install pdo_pgsql 

RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g teamplanning teamplanning

COPY . /var/www
COPY --chown=teamplanning:teamplanning

USER teamplanning
 
EXPOSE 9000
 
CMD ["php-fpm"]
# docker build Dockerfile -t [Cedrix73]/[teamplanning]
