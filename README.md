# Course Platform API

REST API for a small course platform, built with Laravel. The project covers authentication, role-based access control, course management, likes, comments, image uploads, and admin moderation.

Frontend: [junior-frontend-app](https://github.com/artushhhd/junior-frontend-app)

## Tech Stack

- PHP 8.3+
- Laravel 13
- Laravel Sanctum 4 — token-based authentication
- Eloquent ORM
- MySQL or SQLite
- PHPUnit

## Features

### Authentication

- User registration and login
- Sanctum token authentication
- Logout and authenticated profile endpoint
- Account blocking through the admin panel
- Blocked users cannot log in

### Courses

- Create, read, update and delete courses
- Image upload
- Course ownership checks
- Like / unlike courses
- Comments
- Admin course approval and moderation

### Roles & Authorization

The application has four roles:

- `user`
- `moderator`
- `admin`
- `superadmin`

Administrative routes are protected by middleware and role-based authorization rules.

Some examples:

- Moderators cannot manage courses created by admins or superadmins
- Admins cannot block or delete other admins
- Only a superadmin can manage a superadmin account

## Project Structure

```text
app/
├── Http/
│   ├── Controllers/   # API controllers
│   ├── Middleware/    # Authentication / admin middleware
│   └── Requests/      # Request validation
├── Models/            # Eloquent models
└── UserRole.php       # User role definitions

routes/
└── api.php            # API routes

database/
└── migrations/       # Database schema
```

## Main API Endpoints

```text
POST   /api/register
POST   /api/login
POST   /api/logout                (auth)
GET    /api/profile               (auth)

GET    /api/courses
GET    /api/courses/{id}
POST   /api/courses               (auth)
PUT    /api/courses/{id}          (auth, owner)
DELETE /api/courses/{id}          (auth, owner)
POST   /api/courses/{id}/like     (auth)
POST   /api/courses/{id}/comment  (auth)

GET    /api/admin/courses         (admin/moderator)
POST   /api/admin/courses/{id}/approve
DELETE /api/admin/courses/{id}
GET    /api/admin/users
POST   /api/admin/users/{id}/toggle-block
DELETE /api/admin/users/{id}
```

## Installation

### 1. Install dependencies

```bash
composer install
```

### 2. Configure the environment

```bash
cp .env.example .env
php artisan key:generate
```

The included `.env.example` uses SQLite by default. MySQL can be configured by changing the `DB_*` variables in `.env`.

### 3. Run migrations

```bash
php artisan migrate
```

### 4. Start the API

```bash
php artisan serve
```

The API is available at:

```text
http://127.0.0.1:8000
```

### 5. Run tests

```bash
php artisan test
```

## Frontend

The corresponding Next.js frontend is available here:

**[junior-frontend-app](https://github.com/artushhhd/junior-frontend-app)**
