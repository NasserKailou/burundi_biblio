@props(['title', 'description' => null, 'icon' => null])

<div {{ $attributes->class(['mb-6 flex flex-col gap-4 border-b border-bns-border pb-6 sm:flex-row sm:items-center sm:justify-between']) }}>
    <div class="flex items-start gap-3">
        @if ($icon)
            <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-teal-50 text-bns-primary">
                <x-icon :name="$icon" class="h-5 w-5" />
            </span>
        @endif
        <div>
            <h1 class="font-heading text-2xl font-semibold text-bns-foreground">{{ $title }}</h1>
            @if ($description)
                <p class="mt-1 text-sm text-bns-muted-foreground">{{ $description }}</p>
            @endif
        </div>
    </div>

    @isset($actions)
        <div class="flex shrink-0 flex-wrap items-center gap-2">
            {{ $actions }}
        </div>
    @endisset
</div>
