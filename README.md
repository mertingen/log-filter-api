# Instructions

This app has a Symfony command, and it listens and stream a log file that is given as param into that command. It reads each line and send it into the queue to insert into the DB. If there are appending lines, also they're inserted into the DB by queue. 
The following commands will set up a Docker environment for the application. The application container will be connected to the log_filter_api network, allowing it to communicate with the database and cache containers. The web server container will expose port 8080 to allow access to the application from the host machine or the network.

You can follow these instructions to set up and run the application on their local machines or servers using Docker.

## Environment Variables
Create the **.env** file by getting a reference from **.env.example** file. You should fill those variables in **.env** file in root directory of the app.

APP_ENV=

APP_SECRET=

DATABASE_URL=

MESSENGER_TRANSPORT_DSN=

MESSENGER_CONSUMER_NAME=

CACHE_HOST=

CACHE_PORT=

RESPONSE_CACHE_TIMEOUT=

For testing, you should create the **.env.test* file by getting a reference from **.env.test.example** file.

KERNEL_CLASS=

APP_SECRET=

SYMFONY_DEPRECATIONS_HELPER=

PANTHER_APP_ENV=

PANTHER_ERROR_SCREENSHOT_DIR=

DATABASE_URL=

## cURL Command Documentation - Retrieving Count from Localhost
Description:
This document provides details on how to use the cURL command to retrieve a count from the localhost server. The request will be made to the endpoint '127.0.0.1:8080/count', and it will fetch the count for a specific service named 'USER-SERVICE'. Additionally, the 'Response-Cache' header will be utilized in the request with a value of '1'.

cURL Command:
```bash
curl --location --globoff '127.0.0.1:8080/count?serviceNames[]=USER-SERVICE' \
--header 'Response-Cache: 1'
```

'Response-Cache' header specifies that it will only work when there are query string values present in the request.
And, you will get a response header named 'Response-Cache-Timeout'. Thus, that response will be cached until this time.

##  Application Startup Bash Script
If you have **docker-compose** service, you can initialize the app by the following command:

``docker-compose up --build``

If you use only **Docker** the following bash script is used to launch your application in a Docker environment. Follow the steps below to run your application. The script accepts the following optional command-line arguments:

--build: If specified, this argument will trigger the build process for Docker images before starting the containers. If not provided, existing images will be used.

--create-network: If specified, this argument will create a Docker network named log_filter_api to allow communication between containers. If not provided, the script will assume the network already exists or will connect to an existing network with the same name.

To build the Docker images and create the network (if they don't already exist), use the following command:

``./start_app.sh --build --create-network``

To forcefully terminate all running containers and exit the script without proceeding further, use the following command:

``./start_app.sh --kill-containers``

To forcefully remove all running containers and exit the script without proceeding further, use the following command:

``./start_app.sh --rm-containers``

That bash script handles the following steps for you.

## Testing

If you'd like to run test cases you can run the following command in root directory of app.

./vendor/bin/phpunit

## Docker Network

Create a Docker network named **log_filter_api** to allow communication between containers:

``docker network create log_filter_api``

## Database

Build a Docker image named **db_img** for the database:

``docker build database/ -t log-filter-api_db_cont -f database/Dockerfile``

Run the database container named **db_cont** and connect it to the **log_filter_api** network. Expose port **3306** to enable external access to the database:

``docker run -d --name db_cont --network log_filter_api -p 3306:3306 log-filter-api_db_cont``

## Cache

Build a Docker image named **cache_img** for the cache:

``docker build cache/ -t log-filter-api_cache_cont -f cache/Dockerfile``

Run the cache container named **cache_cont** and connect it to the **log_filter_api** network. Expose port **6379** to allow external access to the cache:

``docker run -d --name cache_cont --network log_filter_api -p 6379:6379 log-filter-api_cache_cont``

## Queue

Build a Docker image named **queue_img** for the queue:

``docker build queue/ -t log-filter-api_queue_cont -f queue/Dockerfile``

Run the queue container named **queue_cont** and connect it to the **log_filter_api** network. Expose port **6380** to allow external access to the queue:

``docker run -d --name queue_cont --network log_filter_api -p 6380:6380 log-filter-api_queue_cont``

## App

Build a Docker image named **app_img** for the application:

``docker build app/ -t log-filter-api_app_cont -f app/Dockerfile``

Run the application container named **app_cont**, and mount the current directory **($(pwd))** to **/var/www/app** inside the container. Connect it to the **log_filter_api** network:

``docker run -d --name app_cont -v $(pwd)/app:/var/www/app --network log_filter_api log-filter-api_app_cont``

## Web

Build a Docker image named web_img for the web server:

``docker build web/ -t log-filter-api_web_cont -f web/Dockerfile``

Run the web server container named **web_cont**, and connect it to the **log_filter_api** network. Expose port **8080** to access the application externally:

``docker run -d --name web_cont --network log_filter_api -p 8080:8080 log-filter-api_web_cont``