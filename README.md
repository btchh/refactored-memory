# WashHour - Laundry Management System

A modern laundry booking and management system built with Laravel 12 and Vite.

## Features

- **User Portal**: Book laundry services, track orders, view history
- **Admin Portal**: Manage bookings, customers, pricing, analytics
- **Real-time Messaging**: Chat between users and admins
- **Route Planning**: Map-based delivery tracking with Geoapify
- **Calendar Integration**: CalAPI for scheduling
- **Toast Notifications**: Centralized notification system

## Requirements

- PHP 8.2+
- MySQL 8.0+ or SQLite
- Node.js 18+
- Composer 2.x

## Installation

```bash
# Clone the repository
git clone <repository-url>
cd washhour

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed the database (optional)
php artisan db:seed

# Build assets
npm run build

# Start the server
php artisan serve
```

## Environment Configuration

Copy `.env.example` to `.env` and configure:

```env
# Required
APP_NAME=WashHour
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=washhour
DB_USERNAME=your_username
DB_PASSWORD=your_password

# API Keys (required for full functionality)
CALAPI_KEY=your_calapi_key
GEOAPIFY_API_KEY=your_geoapify_key
IPROG_SMS_API_TOKEN=your_sms_token

# Optional - Real-time messaging
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=ap1
```

## Production Deployment Checklist

### Before Deployment

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Set proper `APP_URL`
- [ ] Configure database credentials
- [ ] Set all required API keys
- [ ] Generate new `APP_KEY` on production server

### Deployment Commands

```bash
# Install dependencies (no dev)
composer install --no-dev --optimize-autoloader

# Build frontend assets
npm run build

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Set proper permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Security Checklist

- [ ] HTTPS enabled
- [ ] Database credentials secured
- [ ] API keys not exposed in frontend
- [ ] CSRF protection enabled (default)
- [ ] Session encryption enabled
- [ ] Rate limiting configured

### Performance Optimization

```bash
# Enable OPcache in php.ini
opcache.enable=1
opcache.memory_consumption=256

# Use Redis for sessions/cache (recommended)
SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis
```

## Default Accounts

After seeding, use these credentials:

**Admin:**
- Email: admin@washhour.com
- Password: password

**User:**
- Email: user@washhour.com
- Password: password

## API Integrations

| Service | Purpose | Required |
|---------|---------|----------|
| CalAPI | Calendar/Scheduling | Yes |
| Geoapify | Maps/Routing | Yes |
| iProg SMS | OTP/Notifications | Optional |
| Pusher | Real-time messaging | Optional |
| Tawk.to | Live chat widget | Optional |

## License

This project is proprietary software.
