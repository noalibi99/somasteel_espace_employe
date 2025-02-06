# Project Setup

## Requirements
- PHP >= 8.0
- Composer
- MySQL 
- Node.js & NPM 

## Setup Steps

1. **Clone the repository**:

   ```bash
   git clone https://github.com/San-AMRANI/somasteel_espace_employe.git
   cd somasteel_espace_employe
   ```

2. **Install PHP dependencies**:

   Make sure you have Composer installed, then run:

   ```bash
   composer install
   ```

3. **Set up your environment file**:

   Copy the `.env.example` to `.env`:

   ```bash
   cp .env.example .env
   ```

   Update the `.env` file with your specific environment settings (e.g., database credentials).

4. **Generate application key**:

   ```bash
   php artisan key:generate
   ```

5. **Set up the database**:

   Update the database settings in `.env`, then run migrations to create the necessary tables:

   ```bash
   php artisan migrate
   ```
   Then seed the tables with the necessary data:

   ```bash
   php artisan db:seed --class=ServicesTableSeeder
   php artisan db:seed --class=UsersTableSeeder
   ```

6. **Install frontend dependencies** (if applicable):

   For NPM:

   ```bash
   npm install
   ```
7. **Compile frontend assets** (optional, if using frontend build tools):

   ```bash
   npm run dev
   ```
8. **Serve the application**:

   Start the development server:

   ```bash
   php artisan serve
   ```

   Visit `http://localhost:8000` to see your project in action.


*Developed by [AMRANI Hassan]*
