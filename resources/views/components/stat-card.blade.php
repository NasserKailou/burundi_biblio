@props(['label', 'value', 'icon' => null, 'accent' => 'primary'])

@php
$accents = [
    'primary' => 'bg-teal-600/10 text-bns-primary',
    'accent' => 'bg-amber-500/10 text-bns-on-accent',
    'success' => 'bg-emerald-500/10 text-emerald-700',
    'muted' => 'bg-bns-muted text-bns-muted-foreground',
];
@endphp

<div {{ $attributes->class('rounded-xl border border-bns-border bg-bns-card p-6 shadow-sm transition-shadow hover:shadow-md') }}>
    <div class="flex items-start justify-between">
        <p class="text-sm font-medium uppercase tracking-wide text-bns-muted-foreground">{{ $label }}</p>
        @if ($icon)
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $accents[$accent] ?? $accents['primary'] }}">
                <x-icon :name="$icon" class="h-[18px] w-[18px]" />
            </span>
        @endif
    </div>
    <p class="mt-2 text-3xl font-heading font-semibold text-bns-foreground">{{ $value }}</p>
</div>
