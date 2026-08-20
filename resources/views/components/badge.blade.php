@props(['color' => 'muted'])

@php
$styles = [
    'muted' => 'bg-bns-muted text-bns-muted-foreground',
    'primary' => 'bg-teal-50 text-bns-primary',
    'accent' => 'bg-amber-50 text-bns-on-accent',
    'success' => 'bg-emerald-50 text-emerald-700',
    'destructive' => 'bg-red-50 text-bns-destructive',
];
@endphp

<span {{ $attributes->class([
    'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
    $styles[$color] ?? $styles['muted'],
]) }}>
    {{ $slot }}
</span>
