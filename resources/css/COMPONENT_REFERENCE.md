# Component Class Reference

This document provides examples of how to use the core component classes implemented in the UI/UX revamp.

## Button Components

### Basic Usage

```html
<!-- Primary Button -->
<button class="btn btn-primary">Save Changes</button>

<!-- Secondary Button -->
<button class="btn btn-secondary">Cancel</button>

<!-- Outline Button -->
<button class="btn btn-outline">Learn More</button>
```

### Button Sizes

```html
<!-- Small Button -->
<button class="btn btn-primary btn-sm">Small</button>

<!-- Default Button -->
<button class="btn btn-primary">Default</button>

<!-- Large Button -->
<button class="btn btn-primary btn-lg">Large</button>
```

### Button States

```html
<!-- Disabled Button -->
<button class="btn btn-primary" disabled>Disabled</button>

<!-- Full Width Button -->
<button class="btn btn-primary btn-block">Full Width</button>

<!-- Icon-only Button -->
<button class="btn btn-primary btn-icon" aria-label="Delete">
    <svg>...</svg>
</button>
```

## Form Input Components

### Text Input

```html
<div class="form-group">
    <label class="form-label">
        Email Address
        <span class="required">*</span>
    </label>
    <input type="email" class="form-input" placeholder="Enter your email">
    <p class="form-help">We'll never share your email with anyone else.</p>
</div>
```

### Input with Error

```html
<div class="form-group">
    <label class="form-label">Username</label>
    <input type="text" class="form-input error" value="ab">
    <p class="form-error">
        <svg>...</svg>
        Username must be at least 3 characters
    </p>
</div>
```

### Select Dropdown

```html
<div class="form-group">
    <label class="form-label">Service Type</label>
    <select class="form-select">
        <option>Select a service</option>
        <option>Wash & Fold</option>
        <option>Dry Cleaning</option>
    </select>
</div>
```

### Textarea

```html
<div class="form-group">
    <label class="form-label">Special Instructions</label>
    <textarea class="form-textarea" placeholder="Any special requests?"></textarea>
</div>
```

### Checkbox & Radio

```html
<!-- Checkbox -->
<div class="flex items-center">
    <input type="checkbox" id="terms" class="form-checkbox">
    <label for="terms" class="form-check-label">I agree to the terms</label>
</div>

<!-- Radio -->
<div class="flex items-center">
    <input type="radio" id="option1" name="option" class="form-radio">
    <label for="option1" class="form-check-label">Option 1</label>
</div>
```

### Input with Icon

```html
<div class="form-group">
    <label class="form-label">Search</label>
    <div class="input-group">
        <svg class="input-group-icon w-5 h-5">...</svg>
        <input type="text" class="form-input form-input-icon" placeholder="Search...">
    </div>
</div>
```

## Card Components

### Basic Card

```html
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Card Title</h3>
        <p class="card-subtitle">Optional subtitle</p>
    </div>
    <div class="card-body">
        <p>Card content goes here</p>
    </div>
    <div class="card-footer">
        <button class="btn btn-primary">Action</button>
    </div>
</div>
```

### Stat Card (Dashboard Metrics)

```html
<div class="stat-card">
    <div class="stat-icon-container">
        <svg class="stat-icon">...</svg>
    </div>
    <p class="stat-label">Total Bookings</p>
    <p class="stat-value">1,234</p>
    <p class="stat-change positive">
        <svg>...</svg>
        +12% from last month
    </p>
</div>
```

### Action Card (Quick Actions)

```html
<div class="action-card">
    <div class="action-card-icon">
        <svg>...</svg>
    </div>
    <h4 class="action-card-title">New Booking</h4>
    <p class="action-card-description">Schedule a new laundry service</p>
</div>
```

### Admin Stat Card (with colored border)

```html
<div class="admin-stat-card primary">
    <div class="stat-icon-container">
        <svg class="stat-icon">...</svg>
    </div>
    <p class="stat-label">Active Users</p>
    <p class="stat-value">856</p>
</div>
```

### Card Grid Layouts

```html
<!-- 3-column grid -->
<div class="card-grid card-grid-3">
    <div class="stat-card">...</div>
    <div class="stat-card">...</div>
    <div class="stat-card">...</div>
</div>

<!-- 4-column grid -->
<div class="card-grid card-grid-4">
    <div class="action-card">...</div>
    <div class="action-card">...</div>
    <div class="action-card">...</div>
    <div class="action-card">...</div>
</div>
```

### Empty State

```html
<div class="empty-state">
    <svg class="empty-state-icon">...</svg>
    <h3 class="empty-state-title">No bookings yet</h3>
    <p class="empty-state-description">Get started by creating your first booking</p>
    <button class="btn btn-primary">Create Booking</button>
</div>
```

## Badge and Status Components

### Basic Badges

```html
<!-- Success Badge -->
<span class="badge badge-success">Completed</span>

<!-- Warning Badge -->
<span class="badge badge-warning">Pending</span>

<!-- Error Badge -->
<span class="badge badge-error">Cancelled</span>

<!-- Info Badge -->
<span class="badge badge-info">In Progress</span>

<!-- Neutral Badge -->
<span class="badge badge-neutral">Draft</span>
```

### Badge Sizes

```html
<span class="badge badge-success badge-sm">Small</span>
<span class="badge badge-success">Default</span>
<span class="badge badge-success badge-lg">Large</span>
```

### Badge with Icon

```html
<span class="badge badge-success">
    <svg class="badge-icon">...</svg>
    Completed
</span>
```

### Status Indicators

```html
<!-- Booking Status -->
<span class="status-indicator status-pending">Pending</span>
<span class="status-indicator status-in-progress">In Progress</span>
<span class="status-indicator status-completed">Completed</span>
<span class="status-indicator status-cancelled">Cancelled</span>
```

### Status Dot

```html
<!-- Simple status dot -->
<span class="status-dot success"></span>

<!-- Status with dot and text -->
<div class="status-with-dot">
    <span class="status-dot success"></span>
    <span>Active</span>
</div>

<!-- Pulsing status dot -->
<span class="status-dot success pulse"></span>
```

### Timeline Status

```html
<div class="timeline-status">
    <div class="timeline-status-icon completed">
        <svg>...</svg>
    </div>
    <span class="timeline-status-label completed">Order Received</span>
</div>

<div class="timeline-status">
    <div class="timeline-status-icon active">
        <svg>...</svg>
    </div>
    <span class="timeline-status-label active">In Progress</span>
</div>

<div class="timeline-status">
    <div class="timeline-status-icon pending">
        <svg>...</svg>
    </div>
    <span class="timeline-status-label pending">Ready for Pickup</span>
</div>
```

## Accessibility Notes

- All buttons have a minimum height of 44px for touch accessibility
- All form inputs have a minimum height of 44px
- Focus states include a visible 2px ring
- Icon-only buttons should include `aria-label` attributes
- Form labels should be properly associated with inputs using `for` and `id` attributes
- Status indicators include both color and text/icons for accessibility

## Browser Support

These components are tested and supported in:
- Chrome (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Edge (latest 2 versions)

## Responsive Behavior

- Card grids automatically stack on mobile devices
- Buttons maintain minimum touch targets across all breakpoints
- Form inputs remain accessible on all screen sizes
