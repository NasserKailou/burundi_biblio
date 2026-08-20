@props([
    'variant' => 'primary',
    'type' => 'submit',
    'size' => 'md',
])

@php
$variants = [
    'primary' => 'bg-bns-primary text-bns-on-primary hover:bg-teal-800',
    'secondary' => 'bg-bns-muted text-bns-foreground hover:bg-slate-200 border border-bns-border',
    'danger' => 'bg-bns-destructive text-white hover:bg-red-700',
    'ghost' => 'bg-transparent text-bns-muted-foreground hover:bg-bns-muted',
];

$sizes = [
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-5 py-2.5 text-base',
];
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->class([
        'inline-flex items-center justify-center gap-2 rounded-md font-medium transition-colors duration-150 cursor-pointer',
        'focus:outline-none focus-visible:ring-2 focus-visible:ring-bns-ring focus-visible:ring-offset-2',
        'disabled:cursor-not-allowed disabled:opacity-50',
        $variants[$variant] ?? $variants['primary'],
        $sizes[$size] ?? $sizes['md'],
    ]) }}
>
    {{ $slot }}
</button>
