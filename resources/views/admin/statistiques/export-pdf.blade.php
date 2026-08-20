<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #0f172a; }
        h1 { font-size: 18px; }
        h2 { font-size: 14px; margin-top: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; }
        th { background: #f1f5f9; }
        .kpi { display: inline-block; width: 23%; margin-right: 1%; padding: 10px; border: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <h1>Statistiques - Bibliotheque Numerique Scolaire</h1>
    <p>Genere le {{ now()->format('d/m/Y H:i') }}</p>

    <div>
        <div class="kpi"><strong>{{ $overview['nb_manuels'] }}</strong><br>Manuels</div>
        <div class="kpi"><strong>{{ $overview['nb_consultations'] }}</strong><br>Consultations</div>
        <div class="kpi"><strong>{{ $overview['duree_totale_heures'] }}</strong><br>Heures de lecture</div>
        <div class="kpi"><strong>{{ $overview['nb_eleves_actifs'] }}</strong><br>Eleves actifs</div>
    </div>

    <h2>Manuels les plus consultes</h2>
    <table>
        <thead><tr><th>Manuel</th><th>Consultations</th><th>Duree (s)</th></tr></thead>
        <tbody>
            @foreach ($manuelsPlusConsultes as $ligne)
                <tr><td>{{ $ligne['titre'] }}</td><td>{{ $ligne['nb_consultations'] }}</td><td>{{ $ligne['duree_secondes'] }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <h2>Eleves les plus actifs</h2>
    <table>
        <thead><tr><th>Eleve</th><th>Niveau</th><th>Consultations</th><th>Duree (s)</th></tr></thead>
        <tbody>
            @foreach ($elevesPlusActifs as $ligne)
                <tr><td>{{ $ligne['nom'] }}</td><td>{{ $ligne['niveau'] }}</td><td>{{ $ligne['nb_consultations'] }}</td><td>{{ $ligne['duree_secondes'] }}</td></tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
