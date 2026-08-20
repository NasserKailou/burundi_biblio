@props(['type' => 'info'])

@php
$styles = [
    'success' => 'bg-emerald-50 text-emerald-800',
    'error' => 'bg-red-50 text-red-700',
    'warning' => 'bg-amber-50 text-amber-800',
    'info' => 'bg-slate-100 text-slate-700',
];
@endphp

<div {{ $attributes->class([
    'rounded-md px-4 py-3 text-sm',
    $styles[$type] ?? $styles['info'],
]) }} role="alert">
    {{ $slot }}
</div>
