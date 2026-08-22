/**
 * Palette BNS (charte Burundi) exposee en JS pour Chart.js (docs/design-system.md).
 * Les classes Tailwind/CSS ne sont pas utilisables dans la config JS de Chart.js,
 * ces valeurs doivent donc rester synchronisees avec resources/css/app.css et
 * resources/css/adminlte-skin.css.
 */
export const bnsColors = {
    primary: '#0e7baa',
    primaryBright: '#1ca9e6',
    primaryDeep: '#073c56',
    onPrimary: '#ffffff',
    accent: '#1eb53a',
    onAccent: '#ffffff',
    background: '#f8fafc',
    foreground: '#2b2b2b',
    card: '#ffffff',
    muted: '#f4f7fa',
    mutedForeground: '#475569',
    border: '#e2e8f0',
    success: '#178a2d',
    destructive: '#ce1126',
};

/**
 * Palette categorique pour series de graphiques (matieres, niveaux...).
 * Choisie pour rester distincte y compris pour un daltonisme deuteranope/protanope,
 * ancree sur la charte Burundi (bleu/vert/rouge) puis completee de teintes neutres.
 */
export const bnsChartPalette = [
    '#0e7baa', // bleu action
    '#1eb53a', // vert
    '#ce1126', // rouge
    '#7c3aed', // violet
    '#d97706', // ambre
    '#0891b2', // cyan
    '#65a30d', // lime
    '#9333ea', // violet fonce
];
