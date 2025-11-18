@props([
    'name' => '',
    'label' => '',
    'options' => [],
    'selected' => '',
    'placeholder' => 'Select an option',
    'required' => false,
    'error' => null,
    'helpText' => null,
])

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <select 
        name="{{ $name }}"
        id="{{ $name }}"
        @if($required) required @endif
        {{ $attributes->merge(['class' => 'form-input w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 ' . ($error ? 'border-red-500' : 'border-gray-300')]) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        
        @foreach($options as $value => $label)
            <option value="{{ $value }}" {{ old($name, $selected) == $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
    
    @if($helpText)
        <p class="form-help-text text-sm text-gray-600 mt-1">{{ $helpText }}</p>
    @endif
    
    @if($error)
        <p class="form-error text-sm text-red-500 mt-1">{{ $error }}</p>
    @endif
    
    @error($name)
        <p class="form-error text-sm text-red-500 mt-1">{{ $message }}</p>
    @enderror
</div>
