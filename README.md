# File Service - back (REST API)
The simple file service for the store files

## Stack version
1. PHP 8.2
2. Laravel 10.x
3. MySQL 5.7  
4. Docker

## Install / run
1. git clone https://github.com/morrasan/fileservice-back.git
2. docker compose up
3. open terminal on the `fileservice-app` container and run commands:
    - composer install
    - .env.example -> .env 
    - php artisan migrate
    - php artisan optimize 

## Environment
1. http://localhost:8000 - REST Api 
