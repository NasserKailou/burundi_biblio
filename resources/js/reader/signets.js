/**
 * Signets (marque-pages) stockes cote client (localStorage). Le schema
 * de donnees impose (section 4 du cahier des charges) ne prevoit pas de
 * table dediee aux signets - seule "consultations.derniere_page" est
 * persistee cote serveur pour la reprise de lecture. Les signets sont
 * donc une commodite de navigation locale au navigateur, documentee
 * comme telle dans docs/design-system.md.
 */
const cle = (manuelId) => `bns:signets:${manuelId}`;

export function listerSignets(manuelId) {
    try {
        return JSON.parse(localStorage.getItem(cle(manuelId)) || '[]');
    } catch {
        return [];
    }
}

export function ajouterSignet(manuelId, position) {
    const signets = listerSignets(manuelId);
    signets.push({ position, date: new Date().toISOString() });
    localStorage.setItem(cle(manuelId), JSON.stringify(signets));
    return signets;
}

export function supprimerSignet(manuelId, index) {
    const signets = listerSignets(manuelId);
    signets.splice(index, 1);
    localStorage.setItem(cle(manuelId), JSON.stringify(signets));
    return signets;
}
