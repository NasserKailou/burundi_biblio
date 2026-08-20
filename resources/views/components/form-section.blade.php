@props(['title', 'description' => null, 'last' => false])

<div {{ $attributes->class(['grid gap-6 py-6 first:pt-0 sm:grid-cols-[220px_1fr]', 'border-b border-bns-border' => ! $last]) }}>
    <div>
        <h2 class="font-heading text-sm font-semibold text-bns-foreground">{{ $title }}</h2>
        @if ($description)
            <p class="mt-1 text-sm text-bns-muted-foreground">{{ $description }}</p>
        @endif
    </div>
    <div class="space-y-5">
        {{ $slot }}
    </div>
</div>
