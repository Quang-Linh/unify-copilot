# Installation Manual

## 1. Using `install.sh`

To set up the project, you need to run the `install.sh` script. This script will automate the installation of required dependencies. 

### Steps:
1. Open your terminal.
2. Navigate to the project directory.
3. Run the following command:
   ```bash
   bash install.sh
   ```

## 2. Configuring `.env`

After running the installation script, you need to configure the environment variables in the `.env` file.

### Steps:
1. Copy the example file:
   ```bash
   cp .env.example .env
   ```
2. Edit the `.env` file to set your configurations. You can use any text editor, for example:
   ```bash
   nano .env
   ```

## 3. Running Migrations

Once your environment is configured, you need to run the database migrations.

### Steps:
1. Make sure your database is running and accessible.
2. Run the following command:
   ```bash
   <your_migration_command>
   ```
   (Replace `<your_migration_command>` with the actual command for running migrations, e.g., `php artisan migrate` for Laravel).

## 4. Launching the Application

After the migrations are complete, you can launch the application.

### Steps:
1. Start the application using:
   ```bash
   <your_start_command>
   ```
   (Replace `<your_start_command>` with the actual command to start your application, e.g., `php artisan serve` for Laravel).