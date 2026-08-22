/**
 * Comportements partages du back-office AdminLTE (toasts, confirmations
 * destructrices, initialisation DataTables/Select2, revelation des cartes au
 * chargement). Charge sur toutes les pages authentifiees (layouts/adminlte.blade.php),
 * APRES les scripts jQuery/Bootstrap/AdminLTE/plugins (servis en <script> classiques
 * depuis public/vendor/adminlte par le package jeroennoten/laravel-adminlte, pas via
 * npm) : ce fichier ne fait que consommer les globales jQuery/toastr/Swal deja chargees.
 */
document.addEventListener('DOMContentLoaded', () => {
    const jq = window.jQuery;

    // Toasts (Toastr) aux couleurs Burundi - voir adminlte-skin.css pour .bg-success/.bg-danger.
    if (window.toastr) {
        window.toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 5000,
            extendedTimeOut: 2000,
            preventDuplicates: true,
        };
    }

    // Confirmation des actions destructrices (remplace confirm() natif - coherent
    // avec le retrait de onsubmit="confirm(...)" fait pour la CSP, etape 10).
    document.querySelectorAll('[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmed === 'true') {
                return;
            }
            event.preventDefault();

            const message = form.dataset.confirm || 'Confirmer cette action ?';

            if (window.Swal) {
                window.Swal.fire({
                    title: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Confirmer',
                    cancelButtonText: 'Annuler',
                    confirmButtonColor: '#ce1126',
                    cancelButtonColor: '#0e7baa',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.dataset.confirmed = 'true';
                        form.submit();
                    }
                });
            } else if (window.confirm(message)) {
                form.dataset.confirmed = 'true';
                form.submit();
            }
        });
    });

    // DataTables (recherche/tri/pagination cote client sur les tableaux du back-office).
    if (jq && jq.fn && jq.fn.DataTable) {
        jq('[data-datatable]').each(function initDataTable() {
            jq(this).DataTable({
                language: {
                    search: 'Rechercher :',
                    lengthMenu: 'Afficher _MENU_ lignes',
                    info: '_START_ a _END_ sur _TOTAL_ lignes',
                    infoEmpty: 'Aucune ligne',
                    infoFiltered: '(filtre parmi _MAX_ lignes)',
                    zeroRecords: 'Aucun resultat',
                    paginate: { previous: 'Precedent', next: 'Suivant' },
                },
                pageLength: 25,
                order: [],
            });
        });
    }

    // Select2 (listes deroulantes stylees) sur les selects marques data-select2.
    if (jq && jq.fn && jq.fn.select2) {
        jq('[data-select2]').select2({ theme: 'bootstrap4', width: '100%' });
    }
});
