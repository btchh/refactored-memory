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
    $inputClasses = 'form-input' . ($hasError ? ' error' : '');
@endphp

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required)<span class="text-error">*</span>@endif
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
        <p class="form-help">{{ $hint }}</p>
    @endif
    
    @error($name)
        <p class="form-error">{{ $message }}</p>
    @enderror
    
    @if($error)
        <p class="form-error">{{ $error }}</p>
    @endif
</div>
