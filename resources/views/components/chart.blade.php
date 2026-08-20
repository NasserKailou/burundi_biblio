@props(['type', 'labels', 'values', 'label' => '', 'height' => 260])

<canvas
    data-chart="{{ $type }}"
    data-chart-labels="{{ json_encode($labels) }}"
    data-chart-values="{{ json_encode($values) }}"
    data-chart-label="{{ $label }}"
    height="{{ $height }}"
    role="img"
    aria-label="{{ $label ?: 'Graphique' }}"
></canvas>

{{-- Repli accessible : tableau de donnees equivalent, toujours present dans le DOM --}}
<table class="sr-only">
    <caption>{{ $label }}</caption>
    <thead><tr><th>Categorie</th><th>Valeur</th></tr></thead>
    <tbody>
        @foreach ($labels as $i => $l)
            <tr><td>{{ $l }}</td><td>{{ $values[$i] ?? 0 }}</td></tr>
        @endforeach
    </tbody>
</table>
