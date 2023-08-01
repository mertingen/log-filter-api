# Use official MySQL 8 image as the base
FROM mysql:8

# Set the root password for the database
ENV MYSQL_ROOT_PASSWORD=root
ENV DB_HOST = 0.0.0.0

# Optionally, you can set the character set and collation
# ENV MYSQL_CHARSET=utf8mb4
# ENV MYSQL_COLLATION=utf8mb4_unicode_ci
# ENV TZ=Europe/Istanbul

# Expose the necessary ports to the outside world
EXPOSE 3306

# You can copy SQL files (e.g., table creation) to the "docker-entrypoint-initdb.d" directory.
# These SQL files will be automatically executed when the Docker container is first started.

# Commands that will be automatically executed when the Docker image is run
CMD ["mysqld"]