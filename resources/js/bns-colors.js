/**
 * Palette BNS exposee en JS pour Chart.js (docs/design-system.md).
 * Les classes Tailwind ne sont pas utilisables dans la config JS de Chart.js,
 * ces valeurs doivent donc rester synchronisees avec resources/css/app.css.
 */
export const bnsColors = {
    primary: '#0f766e',
    onPrimary: '#ffffff',
    accent: '#d97706',
    onAccent: '#78350f',
    background: '#f8fafc',
    foreground: '#0f172a',
    card: '#ffffff',
    muted: '#f1f5f9',
    mutedForeground: '#475569',
    border: '#e2e8f0',
    success: '#059669',
    destructive: '#dc2626',
};

/**
 * Palette categorique pour series de graphiques (matieres, niveaux...).
 * Choisie pour rester distincte y compris pour un daltonisme deuteranope/protanope.
 */
export const bnsChartPalette = [
    '#0f766e', // teal
    '#d97706', // amber
    '#2563eb', // blue
    '#7c3aed', // violet
    '#db2777', // pink
    '#65a30d', // lime/green
    '#0891b2', // cyan
    '#b45309', // brown/amber-dark
];
