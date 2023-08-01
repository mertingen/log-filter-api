# Use the official Redis image as the base
FROM redis:latest

# Optionally, you can make configurations for Redis.
# For example, to add password protection, you can uncomment the following two lines.
# ENV REDIS_PASSWORD=my_redis_password
# RUN echo "requirepass ${REDIS_PASSWORD}" >> /usr/local/etc/redis/redis.conf

# Expose the necessary ports to the outside world
EXPOSE 6379

# Commands that will be automatically executed when the Docker image is run
CMD ["redis-server"]