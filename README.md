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
7. [Database](#database)
8. [Routes Overview](#routes-overview)
9. [Project Structure](#project-structure)
10. [Running Tests](#running-tests)
11. [Contributing](#contributing)
12. [License](#license)

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

## Database

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
