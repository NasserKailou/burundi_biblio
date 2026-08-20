import { jsonHeaders } from '../csrf.js';

const INTERVALLE_MAJ_MS = 20000;

/**
 * Suivi de lecture : enregistre l'ouverture du manuel puis met a jour
 * periodiquement la duree de lecture et la derniere position, y compris
 * a la fermeture de l'onglet (fetch keepalive, plus fiable que
 * sendBeacon qui ne supporte que POST).
 */
export class SuiviConsultation {
    constructor(manuelId, getPosition) {
        this.manuelId = manuelId;
        this.getPosition = getPosition;
        this.consultationId = null;
        this.debut = Date.now();
        this.minuteur = null;
    }

    async demarrer() {
        try {
            const reponse = await fetch('/api/consultations', {
                method: 'POST',
                headers: jsonHeaders(),
                body: JSON.stringify({ manuel_id: this.manuelId }),
            });

            if (!reponse.ok) {
                return;
            }

            const { data } = await reponse.json();
            this.consultationId = data.id;
            this.minuteur = setInterval(() => this.envoyerMaj(), INTERVALLE_MAJ_MS);

            window.addEventListener('beforeunload', () => this.envoyerMaj());
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'hidden') {
                    this.envoyerMaj();
                }
            });
        } catch {
            // Le suivi de lecture ne doit jamais bloquer la lecture elle-meme.
        }
    }

    envoyerMaj() {
        if (!this.consultationId) {
            return;
        }

        const dureeSecondes = Math.floor((Date.now() - this.debut) / 1000);
        const dernierePage = this.getPosition();

        fetch(`/api/consultations/${this.consultationId}`, {
            method: 'PATCH',
            headers: jsonHeaders(),
            body: JSON.stringify({ duree_secondes: dureeSecondes, derniere_page: dernierePage }),
            keepalive: true,
        }).catch(() => {});
    }

    arreter() {
        if (this.minuteur) {
            clearInterval(this.minuteur);
        }
        this.envoyerMaj();
    }
}
