@props(['href', 'active' => false, 'icon' => null])

<a
    href="{{ $href }}"
    {{ $attributes->class([
        'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
        'bg-teal-600/10 text-bns-primary' => $active,
        'text-bns-muted-foreground hover:bg-bns-muted hover:text-bns-foreground' => ! $active,
    ]) }}
    @if ($active) aria-current="page" @endif
>
    @if ($icon)
        <x-icon :name="$icon" class="h-[18px] w-[18px] shrink-0 {{ $active ? 'text-bns-primary' : 'text-bns-muted-foreground group-hover:text-bns-foreground' }}" />
    @endif
    <span class="truncate">{{ $slot }}</span>
</a>
