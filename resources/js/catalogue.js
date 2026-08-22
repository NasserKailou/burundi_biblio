const grille = document.getElementById('catalogue-grille');
const statut = document.getElementById('catalogue-statut');
const champRecherche = document.getElementById('recherche');
const champMatiere = document.getElementById('filtre-matiere');

if (grille && champRecherche && champMatiere) {
    let requeteEnCours = null;

    function echapperHtml(valeur) {
        const div = document.createElement('div');
        div.textContent = valeur ?? '';
        return div.innerHTML;
    }

    function carteManuel(manuel) {
        const badgeCommun = manuel.est_commun
            ? '<span class="badge badge-success mt-1">Commun</span>'
            : '';

        return `
            <div class="col-6 col-md-4 col-lg-3 mb-4">
                <a href="${manuel.fiche_url}" class="card h-100 text-decoration-none card-hover">
                    <img src="${manuel.couverture_url}" alt="Couverture du manuel ${echapperHtml(manuel.titre)}" loading="lazy"
                        class="card-img-top" style="aspect-ratio:3/4;object-fit:cover;">
                    <div class="card-body">
                        <p class="mb-1 font-weight-bold text-dark text-truncate">${echapperHtml(manuel.titre)}</p>
                        <p class="mb-0 text-muted small">${echapperHtml(manuel.matiere)}</p>
                        ${badgeCommun}
                    </div>
                </a>
            </div>
        `;
    }

    async function chargerCatalogue() {
        const params = new URLSearchParams();
        if (champRecherche.value.trim()) {
            params.set('q', champRecherche.value.trim());
        }
        if (champMatiere.value) {
            params.set('matiere', champMatiere.value);
        }

        if (requeteEnCours) {
            requeteEnCours.abort();
        }
        requeteEnCours = new AbortController();

        statut.textContent = 'Chargement du catalogue...';

        try {
            const reponse = await fetch(`/api/manuels?${params.toString()}`, {
                headers: { Accept: 'application/json' },
                signal: requeteEnCours.signal,
            });

            if (!reponse.ok) {
                throw new Error(`Erreur ${reponse.status}`);
            }

            const { data, meta } = await reponse.json();

            grille.innerHTML = data.map(carteManuel).join('');
            statut.textContent = meta.total > 0
                ? `${meta.total} manuel(s) trouve(s).`
                : 'Aucun manuel ne correspond a votre recherche.';
        } catch (erreur) {
            if (erreur.name !== 'AbortError') {
                statut.textContent = 'Impossible de charger le catalogue pour le moment.';
            }
        }
    }

    let minuteur;
    champRecherche.addEventListener('input', () => {
        clearTimeout(minuteur);
        minuteur = setTimeout(chargerCatalogue, 300);
    });
    champMatiere.addEventListener('change', chargerCatalogue);

    chargerCatalogue();
}
