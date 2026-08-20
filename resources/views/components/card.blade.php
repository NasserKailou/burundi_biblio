@props(['padded' => true])

<div {{ $attributes->class([
    'rounded-xl border border-bns-border bg-bns-card shadow-sm',
    'p-6' => $padded,
]) }}>
    {{ $slot }}
</div>
