import { Chart, registerables } from 'chart.js';
import { bnsColors, bnsChartPalette } from './bns-colors.js';

Chart.register(...registerables);
Chart.defaults.font.family = "'Source Sans 3', ui-sans-serif, system-ui, sans-serif";
Chart.defaults.color = bnsColors.mutedForeground;

/**
 * Initialise chaque <canvas data-chart="..."> present sur la page.
 * data-chart : type de graphique (bar|line|doughnut)
 * data-chart-labels / data-chart-values : JSON encode via @json Blade
 * (jamais de couleur seule pour porter l'information - libelles toujours
 * visibles en legende/axes, cf. docs/design-system.md section Graphiques).
 */
document.querySelectorAll('[data-chart]').forEach((canvas) => {
    const type = canvas.dataset.chart;
    const labels = JSON.parse(canvas.dataset.chartLabels || '[]');
    const values = JSON.parse(canvas.dataset.chartValues || '[]');
    const libelleSerie = canvas.dataset.chartLabel || '';

    const config = {
        bar: {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: libelleSerie,
                    data: values,
                    backgroundColor: bnsColors.primary,
                    borderRadius: 4,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } },
            },
        },
        line: {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: libelleSerie,
                    data: values,
                    borderColor: bnsColors.primary,
                    backgroundColor: bnsColors.primary,
                    tension: 0.3,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } },
            },
        },
        doughnut: {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: bnsChartPalette,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
            },
        },
    }[type];

    if (config) {
        new Chart(canvas, config);
    }
});
