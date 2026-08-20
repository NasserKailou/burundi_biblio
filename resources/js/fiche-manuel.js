import { jsonHeaders } from './csrf.js';

const conteneur = document.getElementById('actions-manuel');
const bouton = document.getElementById('btn-favori-fiche');

if (conteneur && bouton) {
    let favori = conteneur.dataset.favori === '1';

    bouton.addEventListener('click', async () => {
        try {
            if (favori) {
                await fetch(conteneur.dataset.favoriDestroyUrl, { method: 'DELETE', headers: jsonHeaders() });
            } else {
                await fetch(conteneur.dataset.favoriUrl, {
                    method: 'POST',
                    headers: jsonHeaders(),
                    body: JSON.stringify({ manuel_id: Number(conteneur.dataset.manuelId) }),
                });
            }
            favori = !favori;
            bouton.setAttribute('aria-pressed', String(favori));
            bouton.textContent = favori ? 'Retirer des favoris' : 'Ajouter aux favoris';
        } catch {
            // Echec silencieux : l'etat du bouton reste inchange, l'utilisateur peut reessayer.
        }
    });
}
