// epubjs enregistre ses ViewManagers dans un registre global cle par chaine
// ("default" -> DefaultViewManager) via ePub.register.manager(). Ce registre
// ne survit pas au tree-shaking de Rollup (le lookup dynamique par chaine
// masque la dependance reelle) : "this.ViewManager is not a constructor" est
// leve au premier renderTo(). Contourne en important la classe directement
// et en la passant explicitement via l'option "manager" plutot que la
// chaine "default" (Rendition accepte manager: string | class | instance).
import ePub from 'epubjs/src/index.js';
import DefaultViewManager from 'epubjs/src/managers/default/index.js';

const DELAI_LOCATIONS_MS = 8000;

/**
 * Lecteur EPUB base sur epubjs 0.4.x. Cette version expose ePub(url) comme
 * une factory ASYNCHRONE (elle retourne une Promise, contrairement a l'API
 * 0.3.x ou "new ePub(url)" retournait un Book synchrone) - bug rencontre et
 * corrige en test navigateur reel (TypeError: this.book.renderTo is not a
 * function quand la Promise n'etait pas attendue).
 *
 * Autre difference 0.4.x : book.generateLocations() resout vers un simple
 * tableau de CFI (pas un objet Locations riche avec locationFromCfi/
 * cfiFromLocation comme en 0.3.x) - on reimplemente donc la conversion
 * CFI <-> position entiere nous-memes via ePub.CFI.compare, necessaire car
 * "consultations.derniere_page" est un entier (section 4 du cahier des
 * charges), pas un CFI.
 *
 * L'affichage initial ne depend PAS de la generation des locations (qui
 * parcourt tout l'ouvrage et peut etre lente sur un gros EPUB) : le livre
 * s'affiche immediatement, les locations se generent en arriere-plan avec
 * un delai maximal, et la reprise de lecture applique la position des
 * qu'elles sont pretes. En cas d'echec/timeout, la navigation page a page
 * reste fonctionnelle - seul le suivi de position precise est indisponible.
 */
export class EpubReader {
    constructor(container) {
        this.container = container;
        this.book = null;
        this.rendition = null;
        this.tailleTexte = 100;
        this.locationsPretes = false;
    }

    async charger(url, positionDepart) {
        this.book = await ePub(url);
        this.rendition = this.book.renderTo(this.container, {
            width: '100%',
            height: '100%',
            flow: 'paginated',
            manager: DefaultViewManager,
        });

        await this.rendition.display();

        await this._genererLocations(positionDepart);
    }

    async _genererLocations(positionDepart) {
        try {
            await Promise.race([
                this.book.generateLocations(1000),
                new Promise((_resolve, reject) => setTimeout(() => reject(new Error('timeout locations')), DELAI_LOCATIONS_MS)),
            ]);
            this.locationsPretes = true;

            if (positionDepart) {
                const cfi = this._cfiDepuisPosition(positionDepart);
                if (cfi) {
                    await this.rendition.display(cfi);
                }
            }
        } catch {
            // Degradation gracieuse : navigation suivant/precedent toujours
            // fonctionnelle, simplement sans position numerique precise.
        }
    }

    suivant() {
        return this.rendition.next();
    }

    precedent() {
        return this.rendition.prev();
    }

    zoomAvant() {
        this.tailleTexte = Math.min(this.tailleTexte + 10, 200);
        this.rendition.themes.fontSize(`${this.tailleTexte}%`);
    }

    zoomArriere() {
        this.tailleTexte = Math.max(this.tailleTexte - 10, 60);
        this.rendition.themes.fontSize(`${this.tailleTexte}%`);
    }

    getPosition() {
        if (!this.locationsPretes) {
            return null;
        }
        const position = this.rendition.currentLocation();
        const cfi = position?.start?.cfi;
        if (!cfi) {
            return null;
        }
        const index = this._positionDepuisCfi(cfi);
        return index === null ? null : index + 1;
    }

    getTotal() {
        return this.locationsPretes ? this.book.locations.length : null;
    }

    allerA(positionEntiere) {
        const cfi = this._cfiDepuisPosition(positionEntiere);
        return cfi ? this.rendition.display(cfi) : Promise.resolve();
    }

    _cfiDepuisPosition(positionEntiere) {
        if (!this.locationsPretes) {
            return null;
        }
        return this.book.locations[positionEntiere - 1] ?? null;
    }

    /**
     * Recherche lineaire de l'index le plus proche : les tableaux de
     * locations generes restent de taille modeste (<=1000), largement
     * suffisant en performance pour une recherche ponctuelle (pas par
     * frame).
     */
    _positionDepuisCfi(cfi) {
        const locations = this.book.locations;
        if (!locations?.length) {
            return null;
        }

        for (let i = 0; i < locations.length; i++) {
            if (ePub.CFI.compare(cfi, locations[i]) < 0) {
                return Math.max(0, i - 1);
            }
        }

        return locations.length - 1;
    }
}
