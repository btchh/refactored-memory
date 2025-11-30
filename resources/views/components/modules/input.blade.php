@props([
    'type' => 'text',
    'name' => '',
    'label' => null,
    'placeholder' => '',
    'value' => '',
    'required' => false,
    'readonly' => false,
    'disabled' => false,
    'error' => null,
    'hint' => null,
])

@php
    $hasError = $error || $errors->has($name);
    $inputClasses = 'form-input w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 ' 
        . ($hasError ? 'border-red-500' : 'border-gray-300')
        . ($readonly || $disabled ? ' bg-gray-50' : '');
@endphp

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif
    
    <input 
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($readonly) readonly @endif
        @if($disabled) disabled @endif
        {{ $attributes->merge(['class' => $inputClasses]) }}
    >
    
    @if($hint && !$hasError)
        <p class="text-sm text-gray-500 mt-1">{{ $hint }}</p>
    @endif
    
    @error($name)
        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
    @enderror
    
    @if($error)
        <p class="text-sm text-red-500 mt-1">{{ $error }}</p>
    @endif
</div>
