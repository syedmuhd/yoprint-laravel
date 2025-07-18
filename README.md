## Instructions

Hello there!


1. Depending on your OS, install docker compose v2.

    - Ubuntu: `sudo apt install docker-compose-v2`
    - MacOS: `Go to docker website & download the installer`

2. Clone this project repository, and navigate into the project directory.

3. Run `sudo docker compose up`. Thats it. All dependencies are already included (will auto composer install & npm install & run build): 
    - PHP 8.4
    - Composer 2
    - Npm
    - Nginx
    - Redis
    - MySQL 8.0
    - phpMyAdmin

4. Wait until all docker services are up & npm run build command has finished.

4. Locally, run these commands, in order:
    1. `sudo docker exec laravel-app php artisan migrate`
    2. `sudo docker exec laravel-app php artisan horizon &`
    3. `sudo docker exec laravel-app php artisan reverb:start &`

5. You're done! 

    - Navigate to http://localhost:8080 to try out the project.
    - Navigate to http://localhost:8081 for phpMyAdmin