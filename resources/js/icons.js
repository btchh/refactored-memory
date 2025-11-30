/**
 * Lucide Icons Helper
 * 
 * This module provides easy access to Lucide icons for use throughout the application.
 * Import icons as needed and use them in your Blade templates or JavaScript.
 * 
 * Usage in JavaScript:
 * import { Home, User, Calendar } from './icons';
 * 
 * Usage in Blade (via Alpine.js or inline):
 * <span data-lucide="home"></span>
 */

import { createIcons, icons } from 'lucide';

// Initialize Lucide icons on page load
document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});

// Export commonly used icons for direct import
export {
    // Navigation
    Home,
    Menu,
    X,
    ChevronDown,
    ChevronUp,
    ChevronLeft,
    ChevronRight,
    
    // User & Account
    User,
    UserCircle,
    Users,
    LogIn,
    LogOut,
    Settings,
    
    // Actions
    Plus,
    Minus,
    Edit,
    Trash2,
    Save,
    Check,
    X as Close,
    Search,
    Filter,
    
    // Status
    AlertCircle,
    CheckCircle,
    XCircle,
    Info,
    AlertTriangle,
    
    // Business
    Calendar,
    Clock,
    MapPin,
    Phone,
    Mail,
    CreditCard,
    DollarSign,
    Package,
    ShoppingCart,
    
    // Files & Data
    File,
    FileText,
    Download,
    Upload,
    Eye,
    EyeOff,
    
    // Arrows & Directions
    ArrowLeft,
    ArrowRight,
    ArrowUp,
    ArrowDown,
    
    // Misc
    Star,
    Heart,
    Bell,
    HelpCircle,
    ExternalLink,
    Copy,
    Share2,
} from 'lucide';

/**
 * Helper function to create an icon element
 * @param {string} iconName - Name of the Lucide icon
 * @param {string} className - Additional CSS classes
 * @param {number} size - Icon size in pixels (default: 24)
 * @returns {string} HTML string for the icon
 */
export function icon(iconName, className = '', size = 24) {
    return `<i data-lucide="${iconName}" class="${className}" style="width: ${size}px; height: ${size}px;"></i>`;
}

/**
 * Refresh icons after dynamic content is added
 */
export function refreshIcons() {
    createIcons({ icons });
}
