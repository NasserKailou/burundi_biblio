@props([
    'label' => null,
    'name',
    'required' => false,
    'error' => null,
])

@php
$errorMessage = $error ?? ($errors->has($name) ? $errors->first($name) : null);
$selectId = $attributes->get('id', $name);
@endphp

<div>
    @if ($label)
        <label for="{{ $selectId }}" class="block text-sm font-medium text-bns-foreground">
            {{ $label }}
            @if ($required)
                <span class="text-bns-destructive">*</span>
            @endif
        </label>
    @endif

    <select
        id="{{ $selectId }}"
        name="{{ $name }}"
        @if ($required) required @endif
        {{ $attributes->except('id')->class([
            'mt-1 block w-full rounded-md border bg-white px-3 py-2 shadow-sm transition-colors',
            'focus:outline-none focus-visible:ring-2 focus-visible:ring-bns-ring',
            'border-bns-destructive' => $errorMessage,
            'border-bns-border focus:border-bns-primary' => ! $errorMessage,
        ]) }}
    >
        {{ $slot }}
    </select>

    @if ($errorMessage)
        <p class="mt-1 text-sm text-bns-destructive">{{ $errorMessage }}</p>
    @endif
</div>
