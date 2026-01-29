# Splitwise Laravel API

A Splitwise-like REST API built using Laravel 12 and JWT authentication.

## Features

- User registration & login (JWT)
- Create groups
- Join / leave groups
- Add expenses
- Auto split expenses equally
- Group-wise balance calculation
- Group-wise settlement suggestion
- Cannot leave/delete group with pending dues
- Full feature test coverage

## Tech Stack

- Laravel 12
- MySQL / SQLite
- php-open-source-saver/jwt-auth
- PHPUnit

## Setup

```bash
git clone https://github.com/Ebadur-Rahman1/splitwise-laravel-api.git
cd splitwise-laravel-api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan jwt:secret
php artisan serve
