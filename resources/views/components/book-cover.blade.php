@props(['manuel'])

<div {{ $attributes->class('group') }}>
    <div class="aspect-[3/4] w-full overflow-hidden rounded-lg border border-bns-border bg-bns-muted shadow-sm">
        <img
            src="{{ route('catalogue.couverture', $manuel) }}"
            alt="Couverture du manuel {{ $manuel->titre }}"
            loading="lazy"
            class="h-full w-full object-cover transition-transform duration-200 group-hover:scale-105"
        >
    </div>
    <p class="mt-2 line-clamp-2 font-heading text-sm font-medium text-bns-foreground">{{ $manuel->titre }}</p>
    <p class="text-xs text-bns-muted-foreground">{{ $manuel->matiere->libelle }}</p>
    @if ($manuel->est_commun)
        <x-badge color="accent" class="mt-1">Commun</x-badge>
    @endif
</div>
