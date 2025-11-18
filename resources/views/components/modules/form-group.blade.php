@props([
    'label' => '',
    'name' => '',
    'required' => false,
    'error' => null,
    'helpText' => null,
])

<div class="form-group mb-4">
    @if($label)
        <label for="{{ $name }}" class="form-label block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="form-control">
        {{ $slot }}
    </div>
    
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
