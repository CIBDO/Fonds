@props([
    'tableId' => 'demandes-table',
    'orderCol' => 0,
    'orderDir' => 'desc',
    'excludeLastCol' => true,
    'title' => 'Export DGTCP',
])

<script>
$(document).ready(function() {
    const columnDefs = [];
    @if($excludeLastCol)
    columnDefs.push({ targets: -1, orderable: false, searchable: false, className: 'text-center' });
    @endif

    $('#{{ $tableId }}').DataTable({
        dom: '<"row align-items-center mb-3"<"col-md-6"l><"col-md-6"f>>' +
             '<"row"<"col-12"tr>>' +
             '<"row mt-3"<"col-md-5"i><"col-md-7"p>>',
        language: window.DGTCP_DATATABLES_FR,
        responsive: true,
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, 'Tout']],
        order: [[{{ $orderCol }}, '{{ $orderDir }}']],
        columnDefs: columnDefs
    });

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
        new bootstrap.Tooltip(el);
    });
});
</script>
