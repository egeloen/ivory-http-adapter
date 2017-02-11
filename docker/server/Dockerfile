FROM php:latest

# Workdir
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80

# PHP Server
CMD ["php", "-S", "0.0.0.0:80", "-t", "tests/Fixtures"]
