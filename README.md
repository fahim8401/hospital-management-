# BDHealthSync — Hospital Management System

A full-featured **Hospital Management System** built with **Laravel 12** and **Tailwind CSS v4**. It covers patient registration, appointment scheduling, department & doctor management, invoicing with bKash/Nagad payment stubs, and role-based access control.

---

## Table of Contents

1. [Features](#features)
2. [Tech Stack](#tech-stack)
3. [Requirements](#requirements)
4. [Installation](#installation)
5. [Configuration](#configuration)
6. [Running the Application](#running-the-application)
7. [Deploying to cPanel](#deploying-to-cpanel)
8. [Database](#database)
9. [Admin User Setup](#admin-user-setup)
10. [Routes Overview](#routes-overview)
11. [Project Structure](#project-structure)
12. [Running Tests](#running-tests)
13. [Contributing](#contributing)
14. [License](#license)

---

## Features

- **Patient Management** – Register patients with auto-generated IDs (`PID-YYYYMMDD-XXXX`), store NID, blood group, gender, date of birth, and address. Soft-delete support.
- **Appointment Scheduling** – Book, view, and confirm appointments linked to patients and doctors.
- **Department & Doctor Management** – Organize doctors by department with full CRUD operations.
- **Invoicing** – Generate itemized invoices with sub-total, discount, tax, and grand total. Amounts displayed in Bangladeshi Taka (৳).
- **Payment Gateway Stubs** – Ready-to-extend callback routes for bKash and Nagad mobile payments.
- **Role-Based Access Control** – Built-in `roles` table with extensible user-role relationships.
- **Queue & Caching** – Database-backed queues and cache out of the box.

---

## Tech Stack

| Layer       | Technology                         |
|-------------|-------------------------------------|
| Framework   | Laravel 12 (PHP 8.2+)              |
| Frontend    | Tailwind CSS v4 + Vite 7           |
| Database    | SQLite (default) / MySQL / PostgreSQL |
| Queue       | Laravel Database Queue             |
| Testing     | PHPUnit 11                         |
| Dev Tools   | Laravel Pail, Laravel Pint, Sail   |

---

## Requirements

- **PHP** >= 8.2 with extensions: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`
- **Composer** >= 2.x
- **Node.js** >= 20.x and **npm** >= 10.x
- **SQLite** (default) **or** MySQL / PostgreSQL

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/fahim8401/hospital-management-.git
cd hospital-management-
```

### 2. One-command setup (recommended)

The `composer setup` script handles everything in one go:

```bash
composer run setup
```

This command:
1. Installs PHP dependencies via Composer
2. Copies `.env.example` → `.env` (if not already present)
3. Generates the application key
4. Runs all database migrations
5. Installs Node dependencies
6. Builds frontend assets

### 3. Manual setup (step by step)

```bash
# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create the SQLite database file (default driver)
touch database/database.sqlite

# Run migrations
php artisan migrate

# Install Node dependencies and build assets
npm install
npm run build
```

---

## Configuration

Open `.env` and adjust the following values as needed:

```env
APP_NAME="BDHealthSync HMS"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Default is SQLite — switch to MySQL/PostgreSQL if preferred
DB_CONNECTION=sqlite
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=hospital_management
# DB_USERNAME=root
# DB_PASSWORD=

# Queue & Cache (database driver is used by default)
QUEUE_CONNECTION=database
CACHE_STORE=database
```

---

## Running the Application

### Development (all services in one command)

```bash
composer run dev
```

This starts four concurrent processes:
| Process | Description              |
|---------|--------------------------|
| `server` | `php artisan serve` — application at `http://localhost:8000` |
| `queue`  | `php artisan queue:listen` — processes background jobs |
| `logs`   | `php artisan pail` — real-time log viewer |
| `vite`   | `npm run dev` — hot-module replacement for frontend assets |

### Production build

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan serve
```

### Using Laravel Sail (Docker)

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
```

---

## Deploying to cPanel

> These steps apply to shared hosting providers that use **cPanel** with **MySQL** and **PHP 8.2+** (e.g., Hostinger, Namecheap, BlueHost).

### 1. Build assets locally first

cPanel shared hosting typically does not have Node.js. Build the frontend **on your local machine** before uploading:

```bash
npm install
npm run build
```

This produces a `public/build/` directory that you will upload alongside the rest of the project.

### 2. Upload project files

Upload the entire project to your hosting account. Two common methods:

**Option A — File Manager (cPanel):**
1. Log in to cPanel → **File Manager**.
2. Navigate to your home directory (e.g., `/home/youraccount/`).
3. Create a folder named `hospital-management` (outside `public_html`).
4. Upload a `.zip` of the project, then **Extract** it.

**Option B — FTP/SFTP:**
```bash
# Example using scp (replace values with your host details)
scp -r . youraccount@yourdomain.com:/home/youraccount/hospital-management/
```

### 3. Point your domain to `public/`

1. In cPanel, go to **Domains** → **Addon Domains** or **Subdomains**.
2. Set the **Document Root** to:
   ```
   /home/youraccount/hospital-management/public
   ```
3. If using the main domain, go to **File Manager** and update `public_html` to point there (or create a symlink via Terminal).

### 4. Set PHP version to 8.2+

1. In cPanel, open **MultiPHP Manager** (or **PHP Selector**).
2. Select your domain and choose **PHP 8.2** or higher.
3. Under **PHP Extensions**, ensure these are enabled:
   `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`

### 5. Create the MySQL database

> See [Connecting a MySQL Database (cPanel)](#connecting-a-mysql-database-cpanel) below for the full database setup steps.

### 6. Configure the `.env` file

1. In **File Manager**, navigate to `/home/youraccount/hospital-management/`.
2. Copy `.env.example` to `.env` and edit it:

```env
APP_NAME="BDHealthSync HMS"   # change to your preferred application name
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=youraccount_hospitaldb
DB_USERNAME=youraccount_dbuser
DB_PASSWORD=your_strong_password

SESSION_DRIVER=file
QUEUE_CONNECTION=sync
CACHE_STORE=file
```

> **Note:** Set `SESSION_DRIVER=file` and `CACHE_STORE=file` on shared hosting where database-backed sessions may not be reliable. Set `QUEUE_CONNECTION=sync` unless you have a worker process.

### 7. Install PHP dependencies via cPanel Terminal

1. In cPanel, open **Terminal** (or connect via SSH).
2. Navigate to the project directory:

```bash
cd /home/youraccount/hospital-management
```

3. Install Composer dependencies (no dev tools needed in production):

```bash
composer install --optimize-autoloader --no-dev
```

4. Generate the application key:

```bash
php artisan key:generate
```

5. Run database migrations:

```bash
php artisan migrate --force
```

6. Cache configuration for performance:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. Set file permissions

```bash
chmod -R 755 /home/youraccount/hospital-management
chmod -R 775 /home/youraccount/hospital-management/storage
chmod -R 775 /home/youraccount/hospital-management/bootstrap/cache
```

### 9. Verify the deployment

Open your browser and navigate to `https://yourdomain.com`. If you see a blank page or error, check `storage/logs/laravel.log`:

```bash
tail -n 50 /home/youraccount/hospital-management/storage/logs/laravel.log
```

---

## Database

### Connecting a MySQL Database (cPanel)

Follow these steps inside cPanel to create a MySQL database and user for the application.

#### Step 1 — Create the database

1. In cPanel, go to **MySQL Databases**.
2. Under **Create New Database**, enter a name (e.g., `hospitaldb`) and click **Create Database**.
   - cPanel will prefix it automatically: `youraccount_hospitaldb`.

#### Step 2 — Create a database user

1. Still on the **MySQL Databases** page, scroll to **MySQL Users**.
2. Enter a username (e.g., `dbuser`) and a strong password, then click **Create User**.
   - Full username becomes: `youraccount_dbuser`.

#### Step 3 — Grant privileges

1. Under **Add User to Database**, select your new user and database.
2. Click **Add**, then grant **ALL PRIVILEGES** and save.

#### Step 4 — Update `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=youraccount_hospitaldb
DB_USERNAME=youraccount_dbuser
DB_PASSWORD=your_strong_password
```

#### Step 5 — Run migrations

```bash
# In cPanel Terminal or via SSH
php artisan migrate --force
```

#### Connecting to an external MySQL server

If your MySQL server is on a different host (e.g., a managed database service), update `DB_HOST` and ensure your cPanel account's IP is whitelisted on the remote server:

```env
DB_HOST=db.yourmanagedservice.com
DB_PORT=3306
```

---

### Migrations

Run all migrations:

```bash
php artisan migrate
```

Roll back the last batch:

```bash
php artisan migrate:rollback
```

Fresh migration (drops all tables and re-runs):

```bash
php artisan migrate:fresh
```

### Seed demo data

```bash
php artisan db:seed
```

This creates a default test user:
- **Email:** `test@example.com`
- **Password:** set via `User::factory()` (see `database/seeders/DatabaseSeeder.php`)

### Database schema overview

| Table           | Purpose                                          |
|-----------------|--------------------------------------------------|
| `users`         | System users / staff accounts                   |
| `roles`         | User roles (admin, doctor, receptionist, etc.)  |
| `patients`      | Patient records with auto-generated patient IDs  |
| `departments`   | Hospital departments                             |
| `doctors`       | Doctor profiles linked to departments            |
| `appointments`  | Patient–doctor appointment bookings              |
| `invoices`      | Billing invoices per patient                     |
| `invoice_items` | Line items for each invoice                      |

---

## Admin User Setup

### Option 1 — Create via Laravel Tinker (recommended)

Open a Tinker shell (locally or via cPanel Terminal/SSH):

```bash
php artisan tinker
```

Then run the following commands inside Tinker:

```php
// Create the admin user
$user = \App\Models\User::create([
    'name'     => 'Admin',
    'email'    => 'admin@yourdomain.com',
    'password' => bcrypt('Ch@ngeMe_Adm1n#2024'),
]);

// Create the admin role (if it does not exist yet).
// This uses the application's built-in Role model and model_has_roles pivot table.
$role = \App\Models\Role::firstOrCreate(
    ['name' => 'admin', 'guard_name' => 'web']
);

// Assign the role to the user
\DB::table('model_has_roles')->insert([
    'role_id'    => $role->id,
    'model_type' => \App\Models\User::class,
    'model_id'   => $user->id,
]);

echo "Admin user created: " . $user->email;
```

Press `Ctrl + D` (or type `exit`) to leave Tinker.

### Option 2 — Reset an existing user's password

If a user already exists and you need to change their password:

```bash
php artisan tinker
```

```php
$user = \App\Models\User::where('email', 'admin@yourdomain.com')->firstOrFail();
$user->password = bcrypt('NewP@ssw0rd_Replace_Me!');
$user->save();
echo "Password updated for: " . $user->email;
```

### Option 3 — Use the database seeder

Edit `database/seeders/DatabaseSeeder.php` to set your desired credentials before running:

```php
User::factory()->create([
    'name'     => 'Admin',
    'email'    => 'admin@yourdomain.com',
    'password' => bcrypt('SeederP@ss_Replace_Me!'),
]);
```

Then run:

```bash
php artisan db:seed
```

### Option 4 — Direct SQL (cPanel phpMyAdmin)

If you cannot use the Terminal, log in to **phpMyAdmin** from cPanel and run:

```sql
INSERT INTO users (name, email, password, email_verified_at, created_at, updated_at)
VALUES (
    'Admin',
    'admin@yourdomain.com',
    '$2y$12$REPLACE_WITH_BCRYPT_HASH',
    NOW(),
    NOW(),
    NOW()
);
```

> **Important:** Never store a plain-text password. Generate a bcrypt hash first using an online bcrypt tool or locally with:
> ```bash
> php -r "echo password_hash('ReplaceWithYourOwnPassword!', PASSWORD_BCRYPT, ['cost' => 12]);"
> ```
> Copy the output and paste it in place of `$2y$12$REPLACE_WITH_BCRYPT_HASH`.
> **All example passwords above are placeholders — always choose your own unique, strong password.**

### Password requirements

Use a password that is:
- At least **12 characters** long
- A mix of **uppercase, lowercase, numbers, and symbols**
- Unique to this application (not reused elsewhere)

---

## Routes Overview

| Method | URI                                | Action                        |
|--------|------------------------------------|-------------------------------|
| GET    | `/`                                | Welcome page                  |
| GET    | `/patients`                        | List all patients              |
| POST   | `/patients`                        | Create a new patient           |
| GET    | `/patients/{patient}`              | Show patient details           |
| PUT    | `/patients/{patient}`              | Update patient                 |
| DELETE | `/patients/{patient}`              | Soft-delete patient            |
| GET    | `/appointments`                    | List all appointments          |
| POST   | `/appointments`                    | Book a new appointment         |
| GET    | `/appointments/{appointment}`      | Show appointment details       |
| PUT    | `/appointments/{appointment}`      | Update appointment             |
| DELETE | `/appointments/{appointment}`      | Delete appointment             |
| POST   | `/appointments/{appointment}/confirm` | Confirm an appointment     |
| GET    | `/payments/bkash/callback`         | bKash payment callback (stub)  |
| GET    | `/payments/nagad/callback`         | Nagad payment callback (stub)  |

---

## Project Structure

```
hospital-management-/
├── app/
│   ├── Http/Controllers/
│   │   ├── PatientController.php
│   │   └── AppointmentController.php
│   ├── Models/
│   │   ├── Appointment.php
│   │   ├── Department.php
│   │   ├── Doctor.php
│   │   ├── Invoice.php
│   │   ├── InvoiceItem.php
│   │   ├── Patient.php        # Auto-generates PID-YYYYMMDD-XXXX
│   │   ├── Role.php
│   │   └── User.php
│   ├── Services/              # Business logic services
│   └── Providers/
├── database/
│   ├── migrations/            # All database schema files
│   ├── seeders/               # Demo data seeders
│   └── factories/             # Model factories for testing
├── resources/
│   └── views/                 # Blade templates
├── routes/
│   └── web.php                # Application routes
├── tests/                     # PHPUnit test suite
├── .env.example               # Environment template
├── composer.json
├── package.json
└── vite.config.js
```

---

## Running Tests

```bash
# Using Composer script
composer run test

# Or directly with Artisan
php artisan test

# Run a specific test file
php artisan test tests/Feature/PatientTest.php

# Run with coverage (requires Xdebug or PCOV)
php artisan test --coverage
```

---

## Contributing

1. Fork the repository
2. Create your feature branch: `git checkout -b feature/your-feature-name`
3. Commit your changes: `git commit -m "feat: add your feature"`
4. Push to the branch: `git push origin feature/your-feature-name`
5. Open a Pull Request

Please follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards. You can auto-fix style issues with:

```bash
./vendor/bin/pint
```

---

## License

This project is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT).
