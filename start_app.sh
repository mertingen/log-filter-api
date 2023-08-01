#!/bin/bash

# Default values for flags
build=false
create_network=false
kill_containers=false
rm_containers=false

# Parse the command-line arguments
for arg in "$@"; do
    case "$arg" in
        --build)
            build=true
            ;;
        --create-network)
            create_network=true
            ;;
        --kill-containers)
            kill_containers=true
            ;;
        --rm-containers)
            rm_containers=true
            ;;
        *)
            echo "Unknown argument: $arg"
            exit 1
            ;;
    esac
done

# Kill all running containers if specified
if [ "$kill_containers" = true ]; then
    docker kill $(docker ps -q)
    exit 0
fi

# Forcefully delete all containers if specified
if [ "$rm_containers" = true ]; then
    docker rm -f $(docker ps -aq)
    exit 0
fi

# Create Docker network if specified
if [ "$create_network" = true ]; then
    docker network create log_filter_api
fi

# Build and run the database container
if [ "$build" = true ]; then
    docker build database/ -t log-filter-api_db_cont -f database/Dockerfile
fi
docker run -d --name db_cont --network log_filter_api -p 3306:3306 log-filter-api_db_cont

# Build and run the cache container
if [ "$build" = true ]; then
    docker build cache/ -t log-filter-api_cache_cont -f cache/Dockerfile
fi
docker run -d --name cache_cont --network log_filter_api -p 6379:6379 log-filter-api_cache_cont

# Build and run the queue container
if [ "$build" = true ]; then
    docker build queue/ -t log-filter-api_queue_cont -f queue/Dockerfile
fi
docker run -d --name queue_cont --network log_filter_api -p 6380:6380 log-filter-api_queue_cont

# Build and run the application container
if [ "$build" = true ]; then
    docker build app/ -t log-filter-api_app_cont -f app/Dockerfile
fi
docker run -d --name app_cont -v $(pwd)/app:/var/www/app --network log_filter_api log-filter-api_app_cont

# Build and run the web server container
if [ "$build" = true ]; then
    docker build web/ -t log-filter-api_web_cont -f web/Dockerfile
fi
docker run -d --name web_cont --network log_filter_api -p 8080:8080 log-filter-api_web_cont