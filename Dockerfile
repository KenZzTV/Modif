FROM php:8.2-apache

# 1. On copie tes fichiers d'abord
COPY . /var/www/html/

# 2. ON DONNE LES DROITS (La ligne cruciale)
# On donne la propriété des fichiers à l'utilisateur apache (www-data)
# Et on lui donne les droits d'écriture (777 ou 775)
RUN chown -R www-data:www-data /var/www/html/ \
    && chmod -R 777 /var/www/html/

RUN a2enmod rewrite

EXPOSE 80