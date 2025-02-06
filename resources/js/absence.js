$(document).ready(function () {
    var table = $('#permissions').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.24/i18n/French.json",
        },
        searching: true,
        order: [[0, 'desc']] // Disable default search input
    });
});