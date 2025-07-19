## Instructions

![Screenshot](https://i.postimg.cc/90Z5cPSs/Screenshot-from-2025-07-19-14-31-22.png)

Hello there!


1. Depending on your OS, install docker compose v2.

    - Ubuntu: `sudo apt install docker-compose-v2`
    - MacOS: `Go to docker website & download the installer`

2. Clone this project repository, and navigate into the project directory.

3. Make a new .env file from .env.example, simply `cp .env.example .env`

4. Run `sudo docker compose up`. Thats it. All dependencies are already included (will auto composer install & npm install & run build): 
    - PHP 8.4
    - Composer 2
    - Npm
    - Nginx
    - Redis
    - MySQL 8.0
    - phpMyAdmin

5. Wait until all docker services are up & npm run build command has finished.

6. Locally, run these commands, in order:
    1. `sudo chmod -R 777 storage bootstrap/cache` (bcoz we just mount the project folder to docker, to avoid permission denied during upload)
    1. `sudo docker exec laravel-app php artisan migrate`
    2. `php artisan horizon`
    3. `php artisan reverb:start`

7. You're done! 

    - Navigate to http://localhost:8080 to try out the project.
    - Navigate to http://localhost:8081 for phpMyAdmin
