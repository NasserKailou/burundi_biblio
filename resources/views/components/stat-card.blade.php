@props(['label', 'value'])

<div {{ $attributes->class('rounded-xl border border-bns-border bg-bns-card p-6 shadow-sm') }}>
    <p class="text-sm font-medium uppercase tracking-wide text-bns-muted-foreground">{{ $label }}</p>
    <p class="mt-2 text-3xl font-heading font-semibold text-bns-foreground">{{ $value }}</p>
</div>
