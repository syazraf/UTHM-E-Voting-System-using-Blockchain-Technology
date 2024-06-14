# UTHM-E-Voting-System-using-Blockchain-Technology
# UTHM VOTE SYSTEM

## Project Description
The UTHM VOTE SYSTEM is a blockchain-based voting system designed to ensure secure and transparent voting processes. This project includes a web interface and blockchain backend to manage and conduct elections.

## Prerequisites
Before you begin, ensure you have met the following requirements:
- Node.js and npm installed on your machine.
- Composer installed on your machine.
- PHP installed on your machine.
- MySQL installed and running on your machine.
- Install MetaMask
- Install Ganache

## Installation
Follow these steps to set up the project on your local machine:

1. **Clone the repository:**
    ```sh
    git clone <repository-url>
    ```

2. **Navigate to the project directory:**
    ```sh
    cd UTHM_VOTE_SYSTEM
    ```

3. **Install Node.js dependencies:**
    ```sh
    npm install
    ```

4. **Install PHP dependencies:**
    ```sh
    composer install
    ```

## Configuration
1. **Environment Variables:**
    - Copy the `.env.example` file to `.env` and update the environment variables as needed.
    ```sh
    cp .env.example .env
    ```

2. **Database Configuration:**
    - Ensure your MySQL database is running.
    - Update the `.env` file with your database credentials.

3. **Run Database Migrations:**
    ```sh
    php artisan migrate
    ```

## Running the Project
1. **Start the development server:**
    ```sh
    npm start
    ```

2. **Start the PHP server:**
    ```sh
    php -S localhost:8000 -t public
    ```

3. **Access the application in your web browser:**
    - Open `http://localhost:8000` to access the application.

## Project Structure
- `blockchain/` - Contains blockchain-related code.
- `config/` - Configuration files for the project.
- `css/` - CSS files for styling the frontend.
- `includes/` - PHP includes.
- `public/` - Publicly accessible files.
- `sql/` - SQL files for database setup.
- `vendor/` - Composer dependencies.
