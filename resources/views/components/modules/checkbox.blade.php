@props([
    'name' => '',
    'label' => '',
    'value' => '1',
    'checked' => false,
    'error' => null,
])

<div class="form-group">
    <label class="inline-flex items-center cursor-pointer">
        <input 
            type="checkbox"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ $value }}"
            {{ old($name, $checked) ? 'checked' : '' }}
            {{ $attributes->merge(['class' => 'form-checkbox h-4 w-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500 border-gray-300']) }}
        >
        @if($label)
            <span class="ml-2 text-gray-700">{{ $label }}</span>
        @endif
    </label>
    
    @if($error)
        <p class="form-error text-sm text-red-500 mt-1">{{ $error }}</p>
    @endif
    
    @error($name)
        <p class="form-error text-sm text-red-500 mt-1">{{ $message }}</p>
    @enderror
</div>
