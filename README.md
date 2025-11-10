
## About Task Management System (TMS API)

TMS is an API-based task management system built using the Laravel framework. It provides a robust and scalable solution for managing tasks. The API allows for seamless integration with various front-end applications, enabling registered users to create, update, and track tasks.


### Database
SQLite

## Authentication System
Sanctum


## Testing Tool
PHPUnit


## Server
Laravel Sail (Docker)


## Setup Instructions
To set up the TMS API, follow these steps:
1. Clone the repository to your local machine.
2. Navigate to the project directory.
3. Install the required dependencies using Composer:
   ```bash
   composer install
   ```
4. Copy the `.env.example` file to `.env`:
   ```bash
   cp .env.example .env
   ```
5. Generate the application key:
   ```bash
   php artisan key:generate
   ```
6. Configure the database connection in the `.env` file (SQLite is recommended for simplicity).

7. Run the database migrations:
   ```bash
   php artisan migrate
   ```
8. Set up Laravel Sail:
   ```bash
   composer require laravel/sail --dev

   php artisan sail:install
   ```
9. Start the Laravel Sail:
   ```bash
   ./vendor/bin/sail up
   ```
10. The API will be accessible at `http://localhost:8000`.
