## Line Bot

A Personal assistant on Line Bot

### Installation
  After clone or download the project. Change directory to project root.
- `composer install`
- `yarn` or `npm install`
- `cp .env.example .env` customize your .env file. ex. db setting
- `php artisan key:generate` create your own app key for laravel
- `php artisan migrate` create table and schema. (you have to create database manually first)

### Functions
- weather reports
  - provide specific city/location weather info.
  - provide subscription service and generate daily weather report for the individual user.
