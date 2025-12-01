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

## Railway Deployment (Recommended)

### Quick Deploy

1. **Push to GitHub**
   ```bash
   git add .
   git commit -m "Ready for Railway"
   git push origin main
   ```

2. **Create Railway Project**
   - Go to [railway.app](https://railway.app)
   - Click "New Project" → "Deploy from GitHub repo"
   - Select your repository

3. **Add MySQL Database**
   - In Railway dashboard, click "New" → "Database" → "MySQL"
   - Railway auto-creates `DATABASE_URL`

4. **Set Environment Variables**
   In Railway dashboard → Variables, add:
   ```
   APP_NAME=WashHour
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=base64:YOUR_KEY_HERE
   APP_URL=https://your-app.up.railway.app
   
   DB_CONNECTION=mysql
   MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt
   
   SESSION_DRIVER=database
   SESSION_ENCRYPT=true
   SESSION_SECURE_COOKIE=true
   
   BROADCAST_CONNECTION=pusher
   PUSHER_APP_ID=your_id
   PUSHER_APP_KEY=your_key
   PUSHER_APP_SECRET=your_secret
   PUSHER_APP_CLUSTER=ap1
   
   VITE_PUSHER_APP_KEY=${PUSHER_APP_KEY}
   VITE_PUSHER_APP_CLUSTER=${PUSHER_APP_CLUSTER}
   
   CALAPI_KEY=your_calapi_key
   CALAPI_BASE_URL=https://api.calapi.io
   CALAPI_TIMEZONE=Asia/Manila
   
   GEOAPIFY_API_KEY=your_geoapify_key
   IPROG_SMS_API_TOKEN=your_sms_token
   ```

5. **Generate APP_KEY**
   ```bash
   php artisan key:generate --show
   ```
   Copy the output to Railway's APP_KEY variable.

6. **Deploy**
   Railway auto-deploys on push. Check logs for any issues.

### Railway Environment Variables from MySQL

Railway provides these automatically when you add MySQL:
- `MYSQLHOST`
- `MYSQLPORT`
- `MYSQLDATABASE`
- `MYSQLUSER`
- `MYSQLPASSWORD`

Add these to connect:
```
DB_HOST=${MYSQLHOST}
DB_PORT=${MYSQLPORT}
DB_DATABASE=${MYSQLDATABASE}
DB_USERNAME=${MYSQLUSER}
DB_PASSWORD=${MYSQLPASSWORD}
```

### Post-Deployment

After first deploy, run seeders via Railway CLI:
```bash
railway run php artisan db:seed
```

---

## Manual Deployment

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
