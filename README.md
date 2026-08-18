# ReserHotel

> A premium hotel booking and management platform built with Laravel 12, Blade, Alpine.js, and Tailwind CSS v4.

---

## Table of Contents

- [About](#about)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Requirements](#requirements)
- [Installation](#installation)
- [Docker Setup](#docker-setup)
- [Database](#database)
- [Environment](#environment)
- [Roles & Permissions](#roles--permissions)
- [API](#api)
- [Localization](#localization)
- [Testing](#testing)
- [Security](#security)
- [License](#license)

---

## About

ReserHotel is a full-featured luxury hotel reservation system with a public-facing booking site, a customer dashboard, and an admin panel. It supports multi-hotel management, real-time availability checks, multiple payment gateways, and a REST API — all wrapped in a responsive UI with dark mode and multilingual support (English, Arabic with RTL, and French).

---

## Features

### Public Site
- Hotel browsing with search, filters (city, star rating, price, room type, amenities), and sorting
- Advanced AJAX search with live results
- Hotel detail pages with photo gallery, room listings, reviews, and availability calendar
- Multi-step booking flow: check availability → select room → review → confirm
- In-browser price breakdown with tax calculation

### Customer Dashboard
- Reservations list with status filters and pagination
- Booking history with status and payment badges
- Favorites / saved hotels (toggle on/off)
- Reviews management (submit, edit, delete — pending admin approval)
- Invoices with payment status tracking
- Profile management (name, email, password)
- Real-time notifications (booking confirmed, cancelled, payment, review replies)
- My Bookings page with stats overview and status filtering

### Admin Panel
- Dashboard with revenue stats, occupancy calendar, latest bookings, and latest reviews
- Full CRUD for: Hotels, Room Types, Rooms, Amenities, Galleries, Reviews, Payments, Users
- Reservation workflow: confirm → check-in → check-out → cancel
- Amenity–room assignment management
- Review moderation: approve / reject / reply
- Payment status tracking and updates
- Soft deletes with restore / force-delete for hotels and users
- Role-based access control (admin only)

### REST API (v1)
- Authenticated endpoints via Laravel Sanctum
- Hotels, Rooms, Bookings, Payments, Reviews — full resource APIs
- JSON responses with pagination

### Design & UX
- Custom shadcn-style design token system (CSS variables) with Tailwind CSS v4
- Dark / light mode toggle with system preference detection
- 3D tilt cards, glass morphism, scroll-reveal animations
- Fully responsive — mobile, tablet, desktop
- RTL support (Arabic)
- Bilingual translations (EN / AR / FR) with 16+ language files

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Blade, Alpine.js, Tailwind CSS v4, Vite |
| Icons | Bootstrap Icons, Lucide (SVG) |
| Auth | Laravel Fortify, Laravel Sanctum |
| Permissions | Spatie Laravel Permission |
| Queue | Laravel Horizon |
| Database | SQLite (dev), MySQL 8.0 (prod) |
| Cache / Session | Redis |
| Containerization | Docker (Nginx + PHP-FPM + MySQL + Redis) |
| Code Quality | Laravel Pint, PHPStan |

---

## Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/           # Admin CRUD controllers
│   │   │   ├── Api/V1/          # REST API controllers
│   │   │   ├── Customer/        # Customer dashboard
│   │   │   └── Frontend/        # Public-facing controllers
│   │   └── Middleware/
│   ├── Models/                  # Eloquent models (12 models)
│   ├── Policies/                # Authorization policies (9 policies)
│   ├── Services/                # AvailabilityService, BookingService
│   └── Traits/                  # NotifyAdmins
├── database/
│   ├── factories/               # Model factories
│   ├── migrations/              # 20+ migrations
│   └── seeders/                 # Database seeders with test data
├── docker/                      # Dockerfile, Nginx, MySQL, Supervisor configs
├── lang/
│   ├── en/                      # English (16 files)
│   ├── ar/                      # Arabic
│   └── fr/                      # French
├── resources/
│   ├── css/app.css              # Tailwind v4 + design tokens + component styles
│   ├── js/app.js                # Alpine.js, Lucide, animations, tilt cards
│   └── views/
│       ├── admin/               # Admin panel views
│       ├── auth/                # Login, register, password reset
│       ├── customer/            # Customer dashboard views
│       ├── frontend/            # Public site views
│       └── components/          # Reusable Blade components
├── routes/web.php               # Web routes
├── composer.json
├── package.json
├── vite.config.js
└── docker-compose.yml
```

---

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+
- npm
- SQLite (default) or MySQL 8.0
- Redis (optional, for cache/session/queue in production)

---

## Installation

```bash
# Clone the repository
git clone <repository-url>
cd auth

# Install PHP dependencies
composer install

# Install JS dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create SQLite database (if using SQLite)
touch database/database.sqlite

# Run migrations and seeders
php artisan migrate --seed

# Build frontend assets
npm run build

# Start the dev server
php artisan serve
```

The app will be available at `http://127.0.0.1:8000`.

### Development (all services)

```bash
composer dev
```

This runs the PHP server, queue worker, Vite dev server, and Laravel Pail concurrently.

---

## Docker Setup

The project includes a production-ready Docker Compose configuration with:

- **Nginx** reverse proxy (ports 80/443)
- **4 PHP-FPM** app workers
- **2 Horizon** queue workers
- **1 general** queue worker (4 processes)
- **Scheduler** container
- **MySQL 8.0** with persistent volume
- **Redis** with persistence and memory limits

```bash
# Build and start all containers
docker compose up -d --build

# Run migrations inside a container
docker compose exec php-app-1 php artisan migrate --force

# Seed the database
docker compose exec php-app-1 php artisan db:seed
```

---

## Database

Default is **SQLite** (zero config). For MySQL, update `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reserhotel
DB_USERNAME=root
DB_PASSWORD=secret
```

### Models

| Model | Key Relationships |
|---|---|
| `User` | hasMany: Hotels, Reservations, Reviews, Favorites |
| `Hotel` | belongsTo: User · hasMany: Rooms, RoomTypes, Reservations, Reviews, Galleries |
| `RoomType` | belongsTo: Hotel · hasMany: Rooms |
| `Room` | belongsTo: Hotel, RoomType · hasMany: Reservations, RoomImages |
| `Reservation` | belongsTo: User, Hotel, Room · hasMany: Payments |
| `Payment` | belongsTo: Reservation |
| `Review` | belongsTo: User, Hotel |
| `Amenity` | belongsToMany: Rooms |
| `Gallery` | belongsTo: Hotel · hasMany: Images |
| `Favorite` | belongsTo: User, Hotel |

---

## Environment

Key environment variables (see `.env.example` for full list):

```env
APP_NAME=ReserHotel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=sqlite

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

# Payment Gateways
PAYMENT_DEFAULT_PROVIDER=stripe
STRIPE_SECRET_KEY=
FLUTTERWAVE_SECRET_KEY=
MOLLIE_API_KEY=
PAYPAL_CLIENT_ID=
```

---

## Roles & Permissions

Uses **Spatie Laravel Permission** with three roles:

| Role | Capabilities |
|---|---|
| `admin` | Full access to admin panel, all CRUD, reservation workflow, review moderation |
| `owner` | Hotel management (own properties), API access |
| `guest` | Browse, search, book, review, manage profile and reservations |

Roles are seeded via `RolesAndPermissionsSeeder`. The admin panel is protected by the `admin` middleware. Policies enforce ownership checks across all resource operations.

---

## API

REST API under `/api/v1/` — authenticated via **Laravel Sanctum** tokens.

| Endpoint | Methods |
|---|---|
| `/api/v1/hotels` | `GET` (list, show) |
| `/api/v1/hotels/{hotel}/rooms` | `GET` (list, show) |
| `/api/v1/bookings` | `GET`, `POST` |
| `/api/v1/payments` | `GET`, `POST` |
| `/api/v1/reviews` | `GET`, `POST`, `DELETE` |

All endpoints return JSON with pagination support.

---

## Localization

Three languages with 16 translation files each:

| Language | Locale | Direction |
|---|---|---|
| English | `en` | LTR |
| Arabic | `ar` | RTL |
| French | `fr` | LTR |

Switch language via `GET /language/{locale}`. The layout automatically sets `dir="rtl"` for Arabic.

Translation files cover: auth, booking, admin, search, notifications, validation, and more.

---

## Testing

```bash
# Run all tests
php artisan test

# Run with PHPUnit directly
vendor/bin/phpunit

# Code style check
./vendor/bin/pint --test

# Static analysis
vendor/bin/phpstan analyse
```

---

## Security

A full security audit is documented in [`SECURITY_AUDIT.md`](SECURITY_AUDIT.md). Key measures:

- Passwords hashed via Laravel's `Hash` facade
- CSRF protection on all web routes
- Sanctum token authentication for API
- Rate limiting on auth and booking endpoints
- Ownership checks via Policies on all resource operations
- Soft deletes for data recovery
- Input validation via Form Requests

---

## License

MIT License. See [composer.json](composer.json) for details.
