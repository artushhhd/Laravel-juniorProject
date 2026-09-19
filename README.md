# Course Platform API

A Laravel 13 REST API for a course platform with authentication, role-based authorization, course management, social interactions, moderation, and automated tests.

**Frontend:** [junior-frontend-app](https://github.com/artushhhd/junior-frontend-app)

## What this project demonstrates

- Designing a REST API with Laravel 13
- Token authentication with Laravel Sanctum
- Request validation and protected routes
- Role-based access control with a staff hierarchy
- Resource ownership and authorization rules
- CRUD operations with Eloquent ORM
- Image upload and storage management
- Likes and comments
- Admin moderation and user management
- Pagination for administrative collections
- Feature testing with PHPUnit
- Separation of routing, controllers, requests, models, middleware, policies and database schema

## Tech Stack

| Technology | Usage |
|---|---|
| PHP 8.3+ | Backend |
| Laravel 13 | REST API framework |
| Laravel Sanctum 4 | Token authentication |
| Eloquent ORM | Database access |
| MySQL / SQLite | Database |
| PHPUnit | Automated tests |
| Vite | Laravel frontend tooling |

## Core Features

### Authentication

- User registration and login
- Sanctum bearer-token authentication
- Authenticated profile endpoint
- Logout
- Rate limiting on registration and login
- Active/inactive account state
- Blocked users cannot authenticate

### Courses

- Create, read, update and delete courses
- Course ownership checks
- Image upload and storage
- Like / unlike
- Comments
- Course status and moderation
- Paginated admin course listing

### Roles & Authorization

The API supports four roles:

- `user`
- `moderator`
- `admin`
- `superadmin`

Authorization is enforced on the backend rather than relying on the frontend.

Examples of the role hierarchy:

- Moderators can moderate courses but cannot manage users.
- Moderators cannot manage courses created by admins or superadmins.
- Admins can manage users, but cannot manage other admins or superadmins.
- Only a superadmin can manage a superadmin account.

The frontend role checks are therefore only a UI concern; the API remains the source of truth for authorization.

## API Overview

### Public

```text
POST   /api/register
POST   /api/login

GET    /api/courses
GET    /api/courses/{id}
```

### Authenticated

```text
GET    /api/user
GET    /api/profile
POST   /api/logout

POST   /api/courses
PUT    /api/courses/{id}
PATCH  /api/courses/{id}
DELETE /api/courses/{id}

POST   /api/courses/{id}/like
POST   /api/courses/{id}/comment
```

### Administration

```text
GET    /api/admin/courses
POST   /api/admin/courses/{id}/approve
POST   /api/admin/courses/{id}
DELETE /api/admin/courses/{id}

GET    /api/admin/users
POST   /api/admin/users/{id}/toggle-block
DELETE /api/admin/users/{id}
```

All administrative routes are protected by authentication and the backend admin middleware.

## Architecture

The project keeps responsibilities separated:

```text
app/
├── Http/
│   ├── Controllers/    # HTTP/API orchestration
│   ├── Middleware/     # Authentication and staff access
│   └── Requests/       # Input validation
├── Models/             # Eloquent models and relationships
└── Policies/           # Resource authorization

routes/
└── api.php             # API entry points

database/
├── migrations/         # Database schema
├── factories/          # Test data
└── seeders/            # Initial data

tests/
└── Feature/            # API behaviour and authorization tests
```

The frontend is intentionally kept as a separate application so the API can be consumed independently.

## Testing

Run the complete test suite with:

```bash
php artisan test
```

The test suite covers API behaviour such as course operations, validation, authorization, likes and other application rules.

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/artushhhd/junior-backend-api.git
cd junior-backend-api
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Configure the environment

```bash
cp .env.example .env
php artisan key:generate
```

The example environment is configured for SQLite by default. MySQL can be used by changing the `DB_*` variables in `.env`.

### 4. Prepare the database

```bash
php artisan migrate
```

If you want the seeded development data:

```bash
php artisan migrate:fresh --seed
```

### 5. Link public storage

```bash
php artisan storage:link
```

### 6. Start the API

```bash
php artisan serve
```

The default API server is:

```text
http://127.0.0.1:8000
```

## Frontend

The matching Next.js application is available here:

**[junior-frontend-app](https://github.com/artushhhd/junior-frontend-app)**

It consumes this API through a centralized JavaScript API client and environment-based configuration.

## Project Status

This is a portfolio project focused on demonstrating practical backend development with Laravel: API design, authentication, authorization, validation, persistence, file storage, moderation and testing.

