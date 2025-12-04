# 🧺 WashHour - Modern Laundry Management System

> **Transform your laundry business with intelligent booking, real-time tracking, and seamless customer experience.**

WashHour is a cutting-edge laundry management platform that brings your business into the digital age. Built with Laravel 12 and modern web technologies, it delivers a powerful, intuitive experience for both customers and business owners.

---

## ✨ Why Choose WashHour?

### 🎯 **For Your Customers**
- **Effortless Booking** - Schedule pickups in seconds with our intuitive calendar interface
- **Real-Time Tracking** - Know exactly where your laundry is at every step
- **Smart Branch Selection** - Find the nearest location with interactive maps and distance calculations
- **Instant Notifications** - Stay updated via SMS and in-app notifications
- **Order History** - Access complete booking history and receipts anytime

### 💼 **For Your Business**
- **Comprehensive Dashboard** - Monitor revenue, bookings, and performance at a glance
- **Smart Analytics** - Track online vs walk-in customers, peak hours, and revenue trends
- **Multi-Branch Support** - Manage multiple locations from a single platform
- **Walk-In Support** - Quick booking for guests without requiring registration
- **Route Optimization** - Plan efficient pickup/delivery routes with integrated maps
- **Customer Management** - Complete customer profiles with booking history and preferences

---

## 🚀 Key Features

### 📱 **Customer Portal**
- **Interactive Booking System** - Visual calendar with real-time availability
- **Service Customization** - Choose from multiple services and products
- **Flexible Options** - Pickup or drop-off service types
- **Live Chat** - Direct messaging with your branch
- **Branch Locator** - Interactive map showing all locations with distances
- **Secure Authentication** - OTP-based registration and login

### 🎛️ **Admin Dashboard**
- **Real-Time Analytics** - Revenue tracking, booking trends, and performance metrics
- **Booking Management** - Create, update, and track all orders
- **Customer Insights** - View customer activity and booking patterns
- **Pricing Control** - Manage services, products, and pricing per branch
- **User Management** - Handle customer accounts with status controls
- **Revenue Reports** - Detailed financial reports with export capabilities
- **Delivery Tracking** - Map-based route planning for active pickups

### 🔔 **Smart Notifications**
- **SMS Alerts** - Booking confirmations, status updates, and account notifications
- **Account Status Alerts** - Automatic notifications for suspensions or reactivations
- **Real-Time Updates** - Instant status changes visible to customers
- **Toast Notifications** - Beautiful in-app notifications for all actions

### 🗺️ **Location Intelligence**
- **Interactive Maps** - Powered by Geoapify for accurate routing
- **Distance Calculation** - Automatic distance and ETA computation
- **Branch Grouping** - Smart grouping of multiple admins per location
- **Geocoding** - Automatic address-to-coordinates conversion

### 🔒 **Security & Protection**
- **Account Protection** - Automatic logout for suspended/disabled users
- **Form Protection** - Warns users about unsaved changes
- **Logout Confirmation** - Prevents accidental logouts
- **Session Management** - Secure session handling with CSRF protection
- **Data Validation** - Comprehensive input validation and sanitization

---

## 🎨 Modern User Experience

- **Responsive Design** - Perfect on desktop, tablet, and mobile
- **Dark Mode Ready** - Eye-friendly interface options
- **Smooth Animations** - Polished transitions and interactions
- **Accessible** - WCAG compliant with keyboard navigation
- **Fast Loading** - Optimized assets with Vite bundling
- **Progressive Enhancement** - Works even with JavaScript disabled

---

## 🛠️ Technology Stack

**Backend:**
- Laravel 12 - Modern PHP framework
- MySQL/SQLite - Reliable data storage
- Redis - High-performance caching (optional)

**Frontend:**
- Vite - Lightning-fast build tool
- TailwindCSS + DaisyUI - Beautiful, responsive design
- Alpine.js - Lightweight reactivity
- Lucide Icons - Modern icon library

**Integrations:**
- CalAPI - Calendar scheduling
- Geoapify - Maps and routing
- iProg SMS - OTP and notifications
- Pusher - Real-time messaging (optional)

---

## 📦 Quick Start

### Prerequisites
- PHP 8.2 or higher
- MySQL 8.0+ or SQLite
- Node.js 18+
- Composer 2.x

### Installation

```bash
# Clone the repository
git clone <repository-url>
cd washhour

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate
php artisan db:seed

# Build assets
npm run build

# Start development server
php artisan serve
```

Visit `http://localhost:8000` and start exploring!

---

## 🔑 Default Credentials

**Admin Portal:**
```
Email: admin@washhour.com
Password: password
```

**Customer Portal:**
```
Email: user@washhour.com
Password: password
```

> ⚠️ **Important:** Change these credentials immediately in production!

---

## 🌐 Production Deployment

### Quick Deploy Checklist

```bash
# 1. Install production dependencies
composer install --no-dev --optimize-autoloader

# 2. Build optimized assets
npm run build

# 3. Cache everything for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Run migrations
php artisan migrate --force

# 5. Set permissions
chmod -R 755 storage bootstrap/cache
```

### Environment Configuration

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_DATABASE=washhour
DB_USERNAME=your_username
DB_PASSWORD=your_secure_password

# Required API Keys
CALAPI_KEY=your_calapi_key
GEOAPIFY_API_KEY=your_geoapify_key
IPROG_SMS_API_TOKEN=your_sms_token
```

---

## 📊 Feature Highlights

### Analytics Dashboard
- **Revenue Tracking** - Daily, weekly, monthly, and yearly reports
- **Booking Metrics** - Online vs walk-in customer analysis
- **Performance Indicators** - Completion rates, cancellation rates, average order value
- **Popular Services** - Top-performing services and products
- **Custom Date Ranges** - Flexible reporting periods

### Booking Management
- **Calendar View** - Visual booking calendar with availability
- **Quick Actions** - Fast status updates and rescheduling
- **Weight Tracking** - Record and update laundry weight
- **Service History** - Complete audit trail for each booking
- **Bulk Operations** - Manage multiple bookings efficiently

### Customer Experience
- **One-Click Booking** - Streamlined booking process
- **Branch Comparison** - See all locations with distances
- **Order Tracking** - Real-time status updates
- **Chat Support** - Direct communication with branches
- **Profile Management** - Update details and preferences

---

## 🔌 API Integrations

| Service | Purpose | Status |
|---------|---------|--------|
| **CalAPI** | Booking scheduling & calendar sync | Required |
| **Geoapify** | Maps, routing & geocoding | Required |
| **iProg SMS** | OTP verification & notifications | Recommended |
| **Pusher** | Real-time chat & updates | Optional |

---

## 🎯 Perfect For

- 🏪 **Laundry Shops** - Single or multi-branch operations
- 🚚 **Pickup Services** - Businesses offering pickup/delivery
- 🏢 **Laundromats** - Self-service with booking management
- 🏨 **Hotels** - Guest laundry service management
- 🎓 **Dormitories** - Student laundry booking systems

---

## 📈 Performance

- ⚡ **Fast Loading** - Optimized assets under 500KB
- 🎯 **SEO Ready** - Semantic HTML and meta tags
- 📱 **Mobile First** - Responsive design from the ground up
- ♿ **Accessible** - WCAG 2.1 Level AA compliant
- 🔒 **Secure** - Industry-standard security practices

---

## 🤝 Support & Documentation

Need help? We've got you covered:

- 📚 **Full Documentation** - Comprehensive guides and tutorials
- 💬 **Community Support** - Active developer community
- 🐛 **Issue Tracking** - Report bugs and request features
- 📧 **Email Support** - Direct assistance for critical issues

---

## 📄 License

This project is proprietary software. All rights reserved.

---

## 🌟 Get Started Today!

Transform your laundry business with WashHour. Modern, efficient, and built for growth.

```bash
git clone <repository-url>
cd washhour
composer install && npm install
php artisan migrate --seed
npm run build && php artisan serve
```

**Ready to revolutionize your laundry business? Let's get started! 🚀**

---

<p align="center">Made with ❤️ for the laundry industry</p>
