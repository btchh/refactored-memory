# WashHour Design System Documentation

## Overview

This design system provides a comprehensive set of design tokens, components, and utilities for building consistent, accessible, and modern interfaces in the WashHour laundry management system.

## Design Tokens

### Colors

#### Primary Colors (Blue)
```css
--color-primary-50: #eff6ff
--color-primary-100: #dbeafe
--color-primary-200: #bfdbfe
--color-primary-300: #93c5fd
--color-primary-400: #60a5fa
--color-primary-500: #3b82f6
--color-primary-600: #2563eb (Main Primary)
--color-primary-700: #1d4ed8
--color-primary-800: #1e40af
--color-primary-900: #1e3a8a
```

#### Neutral Colors
```css
--color-gray-50: #f9fafb
--color-gray-100: #f3f4f6
--color-gray-200: #e5e7eb
--color-gray-300: #d1d5db
--color-gray-400: #9ca3af
--color-gray-500: #6b7280
--color-gray-600: #4b5563
--color-gray-700: #374151
--color-gray-800: #1f2937
--color-gray-900: #111827
```

#### Semantic Colors
```css
--color-success: #10b981 (Green)
--color-warning: #f59e0b (Amber)
--color-error: #ef4444 (Red)
--color-info: #3b82f6 (Blue)
```

### Typography

#### Font Family
- Primary: Inter (fallback to system fonts)

#### Font Sizes
- xs: 12px
- sm: 14px
- base: 16px
- lg: 18px
- xl: 20px
- 2xl: 24px
- 3xl: 30px
- 4xl: 36px
- 5xl: 48px
- 6xl: 60px

#### Font Weights
- normal: 400
- medium: 500
- semibold: 600
- bold: 700

#### Line Heights
- tight: 1.25
- normal: 1.5
- relaxed: 1.75

### Spacing

Based on 4px increments:
- 1: 4px
- 2: 8px
- 3: 12px
- 4: 16px
- 5: 20px
- 6: 24px
- 8: 32px
- 10: 40px
- 12: 48px
- 16: 64px

### Border Radius
- sm: 4px
- md: 8px
- lg: 12px
- xl: 16px
- full: 9999px (fully rounded)

### Shadows
- sm: Subtle shadow for cards
- md: Medium shadow for hover states
- lg: Large shadow for elevated elements
- xl: Extra large shadow for modals

## Components

### Buttons

#### Primary Button
```html
<button class="btn btn-primary">Primary Action</button>
```
- Use for primary actions (submit, save, confirm)
- Blue background with white text
- 44px minimum height for accessibility

#### Secondary Button
```html
<button class="btn btn-secondary">Secondary Action</button>
```
- Use for secondary actions (cancel, back)
- Gray background with dark text

#### Outline Button
```html
<button class="btn btn-outline">Outline Action</button>
```
- Use for tertiary actions or less emphasis
- Transparent background with border

#### Button Sizes
```html
<button class="btn btn-primary btn-sm">Small</button>
<button class="btn btn-primary">Default</button>
<button class="btn btn-primary btn-lg">Large</button>
```

### Forms

#### Form Group
```html
<div class="form-group">
    <label class="form-label" for="email">Email Address</label>
    <input type="email" id="email" class="form-input" placeholder="Enter your email">
    <p class="form-help">We'll never share your email.</p>
</div>
```

#### Form Input with Error
```html
<div class="form-group">
    <label class="form-label" for="password">Password</label>
    <input type="password" id="password" class="form-input error">
    <p class="form-error">
        <i data-lucide="alert-circle" class="w-4 h-4"></i>
        Password is required
    </p>
</div>
```

#### Select Dropdown
```html
<select class="form-select">
    <option>Choose an option</option>
    <option>Option 1</option>
    <option>Option 2</option>
</select>
```

#### Textarea
```html
<textarea class="form-textarea" rows="4" placeholder="Enter your message"></textarea>
```

### Cards

#### Standard Card
```html
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Card Title</h3>
    </div>
    <div class="card-body">
        <p>Card content goes here.</p>
    </div>
    <div class="card-footer">
        <button class="btn btn-primary">Action</button>
    </div>
</div>
```

#### Stat Card
```html
<div class="stat-card">
    <div class="stat-icon">
        <i data-lucide="users" class="w-5 h-5"></i>
    </div>
    <div class="stat-label">Total Users</div>
    <div class="stat-value">1,234</div>
    <div class="stat-change text-success">+12% from last month</div>
</div>
```

#### Hover Card
```html
<div class="card card-hover">
    <!-- Card content -->
</div>
```

### Badges

```html
<span class="badge badge-success">Completed</span>
<span class="badge badge-warning">Pending</span>
<span class="badge badge-error">Cancelled</span>
<span class="badge badge-info">In Progress</span>
<span class="badge badge-neutral">Draft</span>
```

### Navigation

#### Navbar
```html
<nav class="navbar">
    <div class="navbar-brand">
        <img src="/logo.png" alt="Logo" class="navbar-logo">
        <span class="navbar-title">WashHour</span>
    </div>
    <div class="navbar-menu">
        <!-- Menu items -->
    </div>
</nav>
```

#### Sidebar
```html
<aside class="sidebar">
    <nav class="sidebar-nav">
        <a href="/dashboard" class="sidebar-link active">
            <i data-lucide="home" class="sidebar-icon"></i>
            <span class="sidebar-label">Dashboard</span>
        </a>
        <a href="/bookings" class="sidebar-link">
            <i data-lucide="calendar" class="sidebar-icon"></i>
            <span class="sidebar-label">Bookings</span>
        </a>
    </nav>
</aside>
```

### Alerts

```html
<div class="alert alert-success">
    <i data-lucide="check-circle" class="alert-icon"></i>
    <div class="alert-content">
        <h4 class="alert-title">Success!</h4>
        <p class="alert-message">Your booking has been confirmed.</p>
    </div>
    <button class="alert-close">×</button>
</div>
```

Available variants: `alert-success`, `alert-warning`, `alert-error`, `alert-info`

### Modals

```html
<div class="modal-backdrop"></div>
<div class="modal-container">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Modal Title</h3>
        </div>
        <div class="modal-body">
            <p>Modal content goes here.</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary">Cancel</button>
            <button class="btn btn-primary">Confirm</button>
        </div>
    </div>
</div>
```

## Icons

### Using Lucide Icons

The design system uses Lucide icons to replace emojis throughout the application.

#### In Blade Templates
```html
<!-- Simple icon -->
<i data-lucide="home"></i>

<!-- Icon with size -->
<i data-lucide="user" class="w-6 h-6"></i>

<!-- Icon with color -->
<i data-lucide="calendar" class="w-5 h-5 text-primary-600"></i>
```

#### In JavaScript
```javascript
import { Home, User, Calendar } from './icons';
import { icon, refreshIcons } from './icons';

// Create icon HTML
const homeIcon = icon('home', 'w-5 h-5 text-gray-600', 20);

// Refresh icons after dynamic content
refreshIcons();
```

#### Common Icons
- Navigation: `home`, `menu`, `x`, `chevron-down`, `chevron-up`
- User: `user`, `user-circle`, `users`, `log-in`, `log-out`
- Actions: `plus`, `minus`, `edit`, `trash-2`, `save`, `check`, `search`
- Status: `alert-circle`, `check-circle`, `x-circle`, `info`, `alert-triangle`
- Business: `calendar`, `clock`, `map-pin`, `phone`, `mail`, `credit-card`

## Utility Classes

### Focus Visible
```html
<button class="focus-visible">Accessible Button</button>
```

### Touch Target
```html
<button class="touch-target">Mobile-Friendly Button</button>
```

### Container
```html
<div class="container-custom">
    <!-- Content with max-width and responsive padding -->
</div>
```

### Section Spacing
```html
<section class="section">
    <!-- Content with consistent vertical padding -->
</section>
```

## Accessibility Guidelines

### Color Contrast
- All text must meet WCAG AA standards (4.5:1 for normal text, 3:1 for large text)
- Use the provided color palette which has been tested for contrast

### Keyboard Navigation
- All interactive elements must be keyboard accessible
- Focus indicators are built into button and form components
- Use `focus-visible` class for custom elements

### Touch Targets
- All interactive elements should be minimum 44x44px
- Use `touch-target` class or built-in component classes

### Screen Readers
- Use semantic HTML elements
- Provide ARIA labels for icon-only buttons
- Associate form labels with inputs

### Reduced Motion
- The design system respects `prefers-reduced-motion`
- Animations are automatically reduced for users who prefer it

## Responsive Design

### Breakpoints
- Mobile: < 768px
- Tablet: 768px - 1023px
- Desktop: ≥ 1024px

### Best Practices
- Use Tailwind's responsive prefixes (sm:, md:, lg:, xl:)
- Stack cards and forms on mobile
- Ensure touch targets are adequate on mobile
- Test at multiple viewport sizes

## Browser Support

- Chrome (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Edge (latest 2 versions)

## Migration Guide

### Replacing Gradients
```html
<!-- Old -->
<div class="bg-gradient-to-r from-blue-500 to-yellow-400">

<!-- New -->
<div class="bg-white border border-gray-200">
```

### Replacing Emojis
```html
<!-- Old -->
<span>👤</span>

<!-- New -->
<i data-lucide="user" class="w-5 h-5"></i>
```

### Updating Buttons
```html
<!-- Old -->
<button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">

<!-- New -->
<button class="btn btn-primary">
```

### Updating Cards
```html
<!-- Old -->
<div class="bg-gradient-to-br from-blue-50 to-yellow-50 p-6 rounded-lg shadow-lg">

<!-- New -->
<div class="card">
    <div class="card-body">
```

## Examples

See the `/examples` directory for complete page examples using the design system.

## Support

For questions or issues with the design system, please contact the development team or refer to the design document at `.kiro/specs/ui-ux-revamp/design.md`.
