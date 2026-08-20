@props(['icon' => 'sparkles', 'title', 'description' => null])

<div {{ $attributes->class(['flex flex-col items-center justify-center gap-3 px-6 py-16 text-center']) }}>
    <span class="flex h-14 w-14 items-center justify-center rounded-full bg-bns-muted text-bns-muted-foreground">
        <x-icon :name="$icon" class="h-7 w-7" />
    </span>
    <div>
        <p class="font-heading text-base font-semibold text-bns-foreground">{{ $title }}</p>
        @if ($description)
            <p class="mt-1 max-w-sm text-sm text-bns-muted-foreground">{{ $description }}</p>
        @endif
    </div>
    @isset($action)
        <div class="mt-2">{{ $action }}</div>
    @endisset
</div>
