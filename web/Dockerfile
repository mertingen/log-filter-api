# Use the official Nginx image as the base
FROM nginx:latest

# Remove the default Nginx configuration file
RUN rm /etc/nginx/conf.d/default.conf \
    && mkdir /var/www && mkdir /var/www/app

# Copy the custom Nginx configuration file for Symfony
COPY nginx.conf /etc/nginx/conf.d/

COPY --from=log-filter-api_app_cont /var/www/app /var/www/app

# Expose the necessary port to the outside world
EXPOSE 8080

# Commands that will be automatically executed when the container is run
CMD ["nginx", "-g", "daemon off;"]