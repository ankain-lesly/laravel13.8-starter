# Mediadock API

Laravel 13 JSON API (`/api/v1`) backend for **Mediadock** — a modern file streaming platform for storing, managing, and streaming media files.

## Features

- Secure authentication (Sanctum)
- User registration, profile management
- Media file upload, download, and streaming endpoints
- Playlists and collections management
- Real-time chat for users
- Platform branding & configuration API

## Not yet implemented

- Advanced analytics & usage stats
- Public sharing links
- Live streaming integrations

Database reset: Avoid using `migrate:fresh` on a database containing important uploads or users to prevent data loss.

## Running Locally

*Requirements:* PHP 8.4+, Node.js

From `server/` directory:

```bash
php artisan migrate --seed
php artisan serve --port=8001
```

Seeded accounts (default password `password`):

- Admin: `admin@mediadock.test` — will redirect to `/admin`
- User: `user@mediadock.test` — will redirect to `/dashboard`

Authentication uses Sanctum (`POST /api/v1/auth/login` with email).

## Production Setup

```bash
composer install --no-dev --optimize-autoloader --no-interaction
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

To build the client (from `client/` directory):

```bash
VITE_ENV=production npm run build
```
