# Reusable Blade Components

This directory contains modular, reusable UI components for the WashHour Laundry Management System.

## Available Components

### 1. Button (`<x-button>`)
```blade
<!-- Primary button -->
<x-button variant="primary" size="md">
    Click Me
</x-button>

<!-- With icon -->
<x-button variant="success" icon="✓">
    Save Changes
</x-button>

<!-- Full width -->
<x-button variant="danger" :fullWidth="true">
    Delete Account
</x-button>

<!-- Variants: primary, secondary, success, danger, warning, outline -->
<!-- Sizes: sm, md, lg -->
```

### 2. Input (`<x-input>`)
```blade
<x-input 
    name="email" 
    type="email"
    label="Email Address"
    placeholder="Enter your email"
    :required="true"
    helpText="We'll never share your email"
/>
```

### 3. Textarea (`<x-textarea>`)
```blade
<x-textarea 
    name="description" 
    label="Description"
    :rows="5"
    placeholder="Enter description..."
/>
```

### 4. Select (`<x-select>`)
```blade
<x-select 
    name="status" 
    label="Status"
    :options="[
        'pending' => 'Pending',
        'active' => 'Active',
        'completed' => 'Completed'
    ]"
    selected="pending"
    :required="true"
/>
```

### 5. Checkbox (`<x-checkbox>`)
```blade
<x-checkbox 
    name="terms" 
    label="I agree to the terms and conditions"
    :checked="false"
/>
```

### 6. Form Group (`<x-form-group>`)
```blade
<x-form-group 
    name="custom_field" 
    label="Custom Field"
    :required="true"
    helpText="This is a custom input">
    <input type="text" name="custom_field" class="form-input">
</x-form-group>
```

### 7. Alert (`<x-alert>`)
```blade
<!-- Success alert -->
<x-alert type="success" :dismissible="true">
    Your changes have been saved!
</x-alert>

<!-- Error alert -->
<x-alert type="error">
    Something went wrong. Please try again.
</x-alert>

<!-- Types: success, error, warning, info -->
```

### 8. Card (`<x-card>`)
```blade
<x-card title="User Profile">
    <p>Card content goes here</p>
    
    <x-slot:footer>
        <x-button>Save</x-button>
    </x-slot:footer>
</x-card>
```

### 9. Badge (`<x-badge>`)
```blade
<x-badge variant="success">Active</x-badge>
<x-badge variant="danger" size="sm">Urgent</x-badge>

<!-- Variants: default, primary, success, danger, warning, info -->
<!-- Sizes: sm, md, lg -->
```

### 10. Table (`<x-table>`)
```blade
<x-table :headers="['Name', 'Email', 'Status', 'Actions']">
    <tr>
        <td>John Doe</td>
        <td>john@example.com</td>
        <td><x-badge variant="success">Active</x-badge></td>
        <td>
            <x-button size="sm">Edit</x-button>
        </td>
    </tr>
</x-table>
```

### 11. Modal (`<x-modal>`)
```blade
<x-modal id="myModal" title="Confirm Action" size="md">
    <p>Are you sure you want to proceed?</p>
    
    <x-slot:footer>
        <x-button variant="secondary" onclick="hideModal('myModal')">
            Cancel
        </x-button>
        <x-button variant="danger">
            Confirm
        </x-button>
    </x-slot:footer>
</x-modal>

<!-- Trigger modal -->
<x-button onclick="showModal('myModal')">Open Modal</x-button>

<!-- Sizes: sm, md, lg, xl -->
```

## Usage Tips

1. All form components automatically handle Laravel validation errors
2. Components use `old()` helper to preserve form data on validation errors
3. Components are styled with utility classes for easy customization
4. Add custom classes using the standard Blade attributes syntax:
   ```blade
   <x-button class="my-custom-class">Button</x-button>
   ```

## Customization

Each component accepts additional HTML attributes that will be merged with default classes:

```blade
<x-input 
    name="email" 
    label="Email"
    class="custom-class"
    data-validation="email"
/>
```
