/**
 * Delegation d'evenement pour les formulaires necessitant une confirmation
 * avant soumission (suppression...). Remplace les attributs inline
 * onsubmit="return confirm(...)" pour permettre un CSP script-src sans
 * 'unsafe-inline' (durcissement securite, section 9 du cahier des charges).
 */
document.addEventListener('submit', (evenement) => {
    const message = evenement.target.dataset?.confirm;

    if (message && ! window.confirm(message)) {
        evenement.preventDefault();
    }
});
