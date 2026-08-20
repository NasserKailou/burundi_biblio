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
            ? '<span class="mt-1 inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-[#78350f]">Commun</span>'
            : '';

        return `
            <a href="${manuel.fiche_url}" class="group block">
                <div class="aspect-[3/4] w-full overflow-hidden rounded-lg border border-bns-border bg-bns-muted shadow-sm">
                    <img src="${manuel.couverture_url}" alt="Couverture du manuel ${echapperHtml(manuel.titre)}" loading="lazy"
                        class="h-full w-full object-cover transition-transform duration-200 group-hover:scale-105">
                </div>
                <p class="mt-2 line-clamp-2 font-heading text-sm font-medium text-bns-foreground">${echapperHtml(manuel.titre)}</p>
                <p class="text-xs text-bns-muted-foreground">${echapperHtml(manuel.matiere)}</p>
                ${badgeCommun}
            </a>
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
