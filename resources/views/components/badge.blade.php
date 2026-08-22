@props(['color' => 'muted'])

@php
$styles = [
    'muted' => 'bg-bns-muted text-bns-muted-foreground',
    'primary' => 'bg-sky-50 text-bns-primary',
    'accent' => 'bg-green-50 text-bns-accent',
    'success' => 'bg-green-50 text-green-800',
    'destructive' => 'bg-red-50 text-bns-destructive',
];
@endphp

<span {{ $attributes->class([
    'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
    $styles[$color] ?? $styles['muted'],
]) }}>
    {{ $slot }}
</span>
