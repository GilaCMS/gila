FROM ubuntu:18.04

RUN apt-get -y update
RUN apt-get -y install apache2

ENV TZ=America/Mexico_City
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get -y install php php-json php-mysql php-mbstring php-zip php-gd
RUN a2enmod rewrite
RUN apt-get -y install wget zip unzip
RUN wget https://github.com/GilaCMS/gila/archive/master.zip
RUN unzip master.zip
RUN mv gila-master/* /var/www/html
RUN mv gila-master/.htaccess /var/www/html
RUN rm /var/www/html/index.html
RUN chmod 777 -R /var/www/html
RUN apt-get clean

EXPOSE 80
COPY tests/scripts/000-default.conf /etc/apache2/sites-available/000-default.conf
CMD ["apache2ctl", "-D", "FOREGROUND"]
