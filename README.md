# Course Platform API (Laravel)

Backend API for a small course platform, built with Laravel + Sanctum. Handles auth, courses, likes/comments and a basic admin panel with roles. Pairs with the frontend here: [Next-juniorProject](https://github.com/artushhhd/Next-juniorProject).

## Stack

- PHP 8.3, Laravel 13
- Laravel Sanctum (token auth)
- MySQL/SQLite via Eloquent + migrations

## What it does

**Auth**
- Register / login / logout with Sanctum tokens
- Accounts can be blocked by an admin (`is_active` flag), blocked users can't log in

**Courses**
- Full CRUD, image upload, only the author can edit/delete their own course
- Likes (many-to-many, toggle on/off)
- Comments

**Roles & admin**
Four roles: `user`, `moderator`, `admin`, `superadmin`. Admin routes are behind an `AdminCheck` middleware. Some rules I added on purpose:
- moderators can't see/touch courses made by admins or superadmin
- admins can't block/delete other admins
- only superadmin can touch a superadmin account

## Project structure

```
app/
├── Http/
│   ├── Controllers/   # UserController, CourseController, AdminController
│   ├── Middleware/    # AdminCheck
│   └── Requests/      # form request validation
├── Models/             # User, Course, CourseComment
└── UserRole.php
routes/api.php
database/migrations/
```

## Main endpoints

```
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

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Runs on `http://127.0.0.1:8000` by default.

## Tests

```bash
php artisan test
```
