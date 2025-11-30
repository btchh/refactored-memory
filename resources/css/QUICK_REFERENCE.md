# Design System Quick Reference

## Quick Start

### 1. Using Buttons
```html
<button class="btn btn-primary">Save</button>
<button class="btn btn-secondary">Cancel</button>
<button class="btn btn-outline">More Options</button>
```

### 2. Using Forms
```html
<div class="form-group">
    <label class="form-label">Email</label>
    <input type="email" class="form-input" placeholder="you@example.com">
</div>
```

### 3. Using Cards
```html
<div class="card">
    <div class="card-body">
        <h3 class="card-title">Title</h3>
        <p>Content here</p>
    </div>
</div>
```

### 4. Using Icons
```html
<i data-lucide="home" class="w-5 h-5"></i>
<i data-lucide="user" class="w-6 h-6 text-primary-600"></i>
```

### 5. Using Badges
```html
<span class="badge badge-success">Completed</span>
<span class="badge badge-warning">Pending</span>
```

## Color Classes

### Primary Colors
- `bg-primary-50` to `bg-primary-900`
- `text-primary-50` to `text-primary-900`
- `border-primary-50` to `border-primary-900`

### Gray Colors
- `bg-gray-50` to `bg-gray-900`
- `text-gray-50` to `text-gray-900`
- `border-gray-50` to `border-gray-900`

### Semantic Colors
- `bg-success`, `text-success`, `border-success`
- `bg-warning`, `text-warning`, `border-warning`
- `bg-error`, `text-error`, `border-error`
- `bg-info`, `text-info`, `border-info`

## Common Patterns

### Centered Auth Form
```html
<div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md">
        <div class="card-body">
            <!-- Form content -->
        </div>
    </div>
</div>
```

### Dashboard Stat Grid
```html
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="stat-card">
        <div class="stat-icon">
            <i data-lucide="users" class="w-5 h-5"></i>
        </div>
        <div class="stat-label">Total Users</div>
        <div class="stat-value">1,234</div>
    </div>
</div>
```

### Alert Message
```html
<div class="alert alert-success">
    <i data-lucide="check-circle" class="alert-icon"></i>
    <div class="alert-content">
        <h4 class="alert-title">Success!</h4>
        <p class="alert-message">Your action was completed.</p>
    </div>
</div>
```

## Icon Reference

### Most Used Icons
- `home` - Home/Dashboard
- `user` - User profile
- `calendar` - Calendar/Bookings
- `clock` - Time
- `check` - Success/Confirm
- `x` - Close/Cancel
- `alert-circle` - Error/Warning
- `info` - Information
- `edit` - Edit action
- `trash-2` - Delete action
- `plus` - Add/Create
- `search` - Search
- `menu` - Menu/Navigation
- `chevron-down` - Dropdown
- `log-out` - Logout

## Spacing Scale

Use Tailwind's spacing utilities:
- `p-1` = 4px, `p-2` = 8px, `p-3` = 12px, `p-4` = 16px
- `p-6` = 24px, `p-8` = 32px, `p-12` = 48px
- Same for `m-*` (margin), `gap-*` (gap), etc.

## Responsive Breakpoints

- `sm:` - 640px and up
- `md:` - 768px and up
- `lg:` - 1024px and up
- `xl:` - 1280px and up

Example:
```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
```

## Accessibility Checklist

- ✅ All buttons have min-height of 44px
- ✅ All form inputs have associated labels
- ✅ Focus states are visible (2px ring)
- ✅ Color contrast meets WCAG AA
- ✅ Icons have text alternatives when needed
- ✅ Keyboard navigation works
- ✅ Reduced motion is respected

## Migration Tips

### Replace Gradients
```html
<!-- Before -->
<div class="bg-gradient-to-r from-blue-500 to-yellow-400">

<!-- After -->
<div class="bg-white border border-gray-200">
```

### Replace Emojis
```html
<!-- Before -->
<span class="text-2xl">👤</span>

<!-- After -->
<i data-lucide="user" class="w-6 h-6 text-gray-600"></i>
```

### Simplify Hover Effects
```html
<!-- Before -->
<div class="hover:scale-105 hover:shadow-2xl transition-all duration-300">

<!-- After -->
<div class="card card-hover">
```

## Need More Help?

See the full documentation: `resources/css/DESIGN_SYSTEM.md`
