@props(['href', 'active' => false])

<a
    href="{{ $href }}"
    {{ $attributes->class([
        'flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors',
        'bg-teal-50 text-bns-primary' => $active,
        'text-bns-muted-foreground hover:bg-bns-muted hover:text-bns-foreground' => ! $active,
    ]) }}
    @if ($active) aria-current="page" @endif
>
    {{ $slot }}
</a>
