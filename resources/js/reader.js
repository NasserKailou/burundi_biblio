import { PdfReader } from './reader/pdf-reader.js';
import { EpubReader } from './reader/epub-reader.js';
import { SuiviConsultation } from './reader/consultation-tracker.js';
import { listerSignets, ajouterSignet, supprimerSignet } from './reader/signets.js';
import { jsonHeaders } from './csrf.js';

const racine = document.getElementById('lecteur');
if (racine) {
    const manuelId = Number(racine.dataset.manuelId);
    const type = racine.dataset.type;
    const fichierUrl = racine.dataset.fichierUrl;
    const dernierePage = racine.dataset.dernierePage ? Number(racine.dataset.dernierePage) : null;
    let favori = racine.dataset.favori === '1';

    const statut = document.getElementById('lecteur-statut');
    const indicateurPosition = document.getElementById('indicateur-position');
    const btnPrecedent = document.getElementById('btn-precedent');
    const btnSuivant = document.getElementById('btn-suivant');
    const btnZoomAvant = document.getElementById('btn-zoom-avant');
    const btnZoomArriere = document.getElementById('btn-zoom-arriere');
    const btnPleinEcran = document.getElementById('btn-plein-ecran');
    const btnFavori = document.getElementById('btn-favori');
    const btnSignets = document.getElementById('btn-signets');
    const panneauSignets = document.getElementById('panneau-signets');
    const btnAjouterSignet = document.getElementById('btn-ajouter-signet');
    const listeSignets = document.getElementById('liste-signets');

    let lecteur;

    function majIndicateur() {
        const position = lecteur.getPosition();
        const total = lecteur.getTotal();
        indicateurPosition.textContent = `${position ?? '-'} / ${total ?? '-'}`;
    }

    function rafraichirSignets() {
        const signets = listerSignets(manuelId);
        listeSignets.innerHTML = signets.length
            ? signets.map((s, i) => `
                <li class="flex items-center justify-between gap-2 rounded px-2 py-1 hover:bg-bns-muted">
                    <button type="button" data-position="${s.position}" class="signet-aller flex-1 truncate text-left">
                        Position ${s.position}
                    </button>
                    <button type="button" data-index="${i}" class="signet-supprimer text-bns-muted-foreground hover:text-bns-destructive" aria-label="Supprimer ce signet">&times;</button>
                </li>
            `).join('')
            : '<li class="text-bns-muted-foreground">Aucun signet.</li>';
    }

    async function initialiser() {
        if (type === 'pdf') {
            document.getElementById('lecteur-pdf-conteneur').classList.remove('hidden');
            document.getElementById('lecteur-pdf-conteneur').classList.add('flex');
            const canvas = document.getElementById('canvas-pdf');
            lecteur = new PdfReader(canvas);
            await lecteur.charger(fichierUrl);
            await lecteur.allerA(dernierePage || 1);
        } else {
            document.getElementById('lecteur-epub-conteneur').classList.remove('hidden');
            const conteneur = document.getElementById('lecteur-epub-conteneur');
            lecteur = new EpubReader(conteneur);
            await lecteur.charger(fichierUrl, dernierePage || undefined);
        }

        majIndicateur();
        statut.textContent = 'Manuel charge.';

        const suivi = new SuiviConsultation(manuelId, () => lecteur.getPosition());
        await suivi.demarrer();
        window.addEventListener('pagehide', () => suivi.arreter());
    }

    btnPrecedent.addEventListener('click', async () => { await lecteur.precedent(); majIndicateur(); });
    btnSuivant.addEventListener('click', async () => { await lecteur.suivant(); majIndicateur(); });
    btnZoomAvant.addEventListener('click', () => lecteur.zoomAvant());
    btnZoomArriere.addEventListener('click', () => lecteur.zoomArriere());

    document.addEventListener('keydown', (evenement) => {
        if (evenement.key === 'ArrowRight') btnSuivant.click();
        if (evenement.key === 'ArrowLeft') btnPrecedent.click();
    });

    btnPleinEcran.addEventListener('click', () => {
        if (document.fullscreenElement) {
            document.exitFullscreen();
        } else {
            racine.requestFullscreen?.();
        }
    });

    btnSignets.addEventListener('click', () => {
        const ouvert = !panneauSignets.classList.contains('hidden');
        panneauSignets.classList.toggle('hidden', ouvert);
        btnSignets.setAttribute('aria-expanded', String(!ouvert));
        if (!ouvert) {
            rafraichirSignets();
        }
    });

    btnAjouterSignet.addEventListener('click', () => {
        ajouterSignet(manuelId, lecteur.getPosition());
        rafraichirSignets();
    });

    listeSignets.addEventListener('click', (evenement) => {
        const btnAller = evenement.target.closest('.signet-aller');
        const btnSupprimer = evenement.target.closest('.signet-supprimer');

        if (btnAller) {
            lecteur.allerA(Number(btnAller.dataset.position));
            majIndicateur();
        }
        if (btnSupprimer) {
            supprimerSignet(manuelId, Number(btnSupprimer.dataset.index));
            rafraichirSignets();
        }
    });

    function majIconeFavori() {
        btnFavori.setAttribute('aria-pressed', String(favori));
        btnFavori.classList.toggle('text-amber-400', favori);
    }
    majIconeFavori();

    btnFavori.addEventListener('click', async () => {
        try {
            if (favori) {
                await fetch(racine.dataset.favoriDestroyUrl, { method: 'DELETE', headers: jsonHeaders() });
            } else {
                await fetch(racine.dataset.favoriUrl, {
                    method: 'POST',
                    headers: jsonHeaders(),
                    body: JSON.stringify({ manuel_id: manuelId }),
                });
            }
            favori = !favori;
            majIconeFavori();
        } catch {
            statut.textContent = "Impossible de mettre a jour les favoris pour le moment.";
        }
    });

    initialiser().catch((erreur) => {
        console.error('Erreur initialisation lecteur', erreur);
        statut.textContent = 'Une erreur est survenue lors du chargement du manuel.';
    });
}
