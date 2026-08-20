@props([
    'label' => null,
    'name',
    'type' => 'text',
    'required' => false,
    'error' => null,
    'help' => null,
])

@php
$errorMessage = $error ?? ($errors->has($name) ? $errors->first($name) : null);
$inputId = $attributes->get('id', $name);
@endphp

<div>
    @if ($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-bns-foreground">
            {{ $label }}
            @if ($required)
                <span class="text-bns-destructive">*</span>
            @endif
        </label>
    @endif

    <input
        id="{{ $inputId }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if ($required) required @endif
        {{ $attributes->except('id')->class([
            'mt-1 block w-full rounded-md border px-3 py-2 shadow-sm transition-colors',
            'focus:outline-none focus-visible:ring-2 focus-visible:ring-bns-ring',
            'border-bns-destructive' => $errorMessage,
            'border-bns-border focus:border-bns-primary' => ! $errorMessage,
        ]) }}
    >

    @if ($errorMessage)
        <p class="mt-1 text-sm text-bns-destructive">{{ $errorMessage }}</p>
    @elseif ($help)
        <p class="mt-1 text-sm text-bns-muted-foreground">{{ $help }}</p>
    @endif
</div>
