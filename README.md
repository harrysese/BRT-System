# PHP Backend Project

This is a simple PHP backend project built without using any framework. It includes basic routing, environment variable management, and logging capabilities. The project structure is organized for easy expansion as the application grows.

## Features

- Basic routing for GET and POST requests
- Environment variable management using [PHP-Dotenv](https://github.com/vlucas/phpdotenv)
- Logging using [Monolog](https://github.com/Seldaek/monolog)

## Getting Started

### Prerequisites

- PHP (>=7.4)
- Composer

### Installation

1. **Clone the Repository**

   ```bash
   git clone https://github.com/No-bodyq/BRT-System.git
   cd backend

   ```

2. **Install Composer Dependencies**

   ```bash
   composer install

   ```

3. **Install Dependencies**

   ```bash
   composer require vlucas/phpdotenv monolog/monolog

   ```

4. **Create `.env` File for Environment Variables**

   ```bash
   touch .env

   ```

   **Add environment variables in `.env`:**

   ```plaintext
    APP_ENV=development
    DB_HOST=localhost
    DB_NAME=<databasename> //your database name
    DB_PORT=<port> //usually 5432
    DB_PASSWORD=<dbpassword> //your database password
    DB_USER="dbuser" //your database username

   ```

5. **Run the Development Server**

```bash
  php -S localhost:8000 -t public

```
