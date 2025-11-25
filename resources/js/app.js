  import './bootstrap';

// Import notification system
import './components/notifications.js';

// Import map modules (will be conditionally loaded based on page)
import './features/tracking/admin-route-map.js';
import './features/tracking/user-track-admin.js';

// Import authentication modules (will be conditionally loaded based on page)
import './features/auth/user-registration.js';
import './features/auth/admin-create.js';
import './features/auth/user-forgot-password.js';
import './features/auth/admin-forgot-password.js';

// Import booking modules
import './features/bookings/user-bookings.js';
