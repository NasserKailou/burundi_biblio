import * as pdfjsLib from 'pdfjs-dist';
import pdfjsWorkerUrl from 'pdfjs-dist/build/pdf.worker.min.mjs?url';

pdfjsLib.GlobalWorkerOptions.workerSrc = pdfjsWorkerUrl;

const DELAI_RENDU_MS = 15000;

/**
 * Lecteur PDF minimal base sur pdfjs-dist, rendu sur <canvas>.
 * pdfjs-dist est vendorise via npm/Vite (pas de CDN) pour fonctionner
 * hors ligne sur l'intranet de l'etablissement.
 */
export class PdfReader {
    constructor(canvas) {
        this.canvas = canvas;
        this.pdf = null;
        this.pageNum = 1;
        this.scale = 1.3;
        this.renduEnCours = false;
        this.rerenduDemande = false;
    }

    async charger(url) {
        this.pdf = await pdfjsLib.getDocument({ url }).promise;
        return this.pdf.numPages;
    }

    async allerA(pageDemandee) {
        const page = Math.min(Math.max(1, pageDemandee), this.pdf.numPages);
        this.pageNum = page;
        return this._rendreCourante();
    }

    suivant() {
        return this.allerA(this.pageNum + 1);
    }

    precedent() {
        return this.allerA(this.pageNum - 1);
    }

    zoomAvant() {
        this.scale = Math.min(this.scale + 0.2, 3);
        return this._rendreCourante();
    }

    zoomArriere() {
        this.scale = Math.max(this.scale - 0.2, 0.5);
        return this._rendreCourante();
    }

    getPosition() {
        return this.pageNum;
    }

    getTotal() {
        return this.pdf.numPages;
    }

    async _rendreCourante() {
        if (this.renduEnCours) {
            this.rerenduDemande = true;
            return;
        }
        this.renduEnCours = true;

        const page = await this.pdf.getPage(this.pageNum);
        const viewport = page.getViewport({ scale: this.scale });
        this.canvas.width = viewport.width;
        this.canvas.height = viewport.height;
        const contexte = this.canvas.getContext('2d');
        const tache = page.render({ canvasContext: contexte, viewport });

        try {
            // Garde-fou : le rendu canvas depend du pipeline de compositing du
            // navigateur (suspendu si l'onglet est en arriere-plan) - sans
            // limite, une page masquee au mauvais moment bloquerait
            // indefiniment plutot que d'afficher une erreur claire.
            await Promise.race([
                tache.promise,
                new Promise((_resolve, reject) => setTimeout(() => reject(new Error('Delai de rendu PDF depasse')), DELAI_RENDU_MS)),
            ]);
        } catch (erreur) {
            tache.cancel();
            this.renduEnCours = false;
            throw erreur;
        }

        this.renduEnCours = false;
        if (this.rerenduDemande) {
            this.rerenduDemande = false;
            await this._rendreCourante();
        }
    }
}
