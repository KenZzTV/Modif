# On utilise une image officielle PHP avec Apache
FROM php:8.2-apache

# On donne les droits d'écriture à Apache sur le dossier web
# C'est CRUCIAL pour que ton PHP puisse modifier data.json et générer les .html
RUN chown -R www-data:www-data /var/www/html/ \
    && chmod -R 755 /var/www/html/

# On active le module de réécriture d'Apache (souvent utile)
RUN a2enmod rewrite

# On copie tous tes fichiers (admin.php, data.json, etc.) dans le serveur
COPY . /var/www/html/

# On expose le port 80
EXPOSE 80