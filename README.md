## Instructions

Hello there!

To simplify your testing/evaluation process, you may try this interview test project online at

        https://yoprint.syedmuhd.my

Or, try it locally using docker (recommended)

1. Depending on your OS, install docker compose v2.

    - Ubuntu: `sudo apt install docker-compose-v2`
    - MacOS: `Go to docker website & download the installer`

2. Clone this project repository, and navigate into the project directory.

3. Run `sudo docker compose up`. Thats it. All dependencies are already included: 
    - PHP 8.4
    - Composer 2
    - Npm
    - Nginx
    - Redis
    - MySQL 8.0
    - phpMyAdmin

4. Wait until "`[Server] /usr/sbin/mysqld: ready for connections.`" message is shown up

4. Locally, run these commands:
    1. `php artisan migrate`
    2. `php artisan horizon`

5. You're done! 

    - Navigate to http://localhost:8080 to try out the project.
    - Navigate to http://localhost:8081 for phpMyAdmin