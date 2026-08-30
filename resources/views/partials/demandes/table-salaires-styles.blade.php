<style>
    .table-salaires-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-salaires {
        width: 100%;
        margin-bottom: 0;
        font-size: 0.9rem;
    }

    .table-salaires th {
        background: var(--bs-gray-50, #f5f5f9);
        color: var(--bs-heading-color, #566a7f);
        font-weight: 600;
        padding: 0.875rem 0.75rem;
        text-align: center;
        vertical-align: middle;
        border: none;
        border-bottom: 2px solid var(--bs-border-color);
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table-salaires th i {
        margin-right: 0.5rem;
        opacity: 0.9;
    }

    .table-salaires tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #e9ecef;
    }

    .table-salaires tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.005);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .table-salaires tbody tr.total-row {
        background: var(--bs-gray-50, #f5f5f9);
        font-weight: 700;
        border-top: 2px solid var(--bs-border-color);
        border-bottom: 2px solid var(--bs-border-color);
    }

    .table-salaires tbody tr.total-row:hover {
        transform: none;
        background: var(--bs-gray-100, #eeedf0);
    }

    .table-salaires tbody tr.total-row td {
        padding: 1rem 0.75rem;
        font-size: 1rem;
    }

    .table-salaires td {
        padding: 0.75rem;
        text-align: center;
        vertical-align: middle;
    }

    .table-salaires td:first-child {
        font-weight: 600;
        text-align: left;
        color: #495057;
        background-color: #f8f9fa;
        position: sticky;
        left: 0;
        z-index: 5;
    }

    .table-salaires tbody tr:hover td:first-child {
        background-color: #e9ecef;
    }

    .table-salaires input[type="text"] {
        width: 100%;
        padding: 0.5rem;
        border: 2px solid #dee2e6;
        border-radius: 0.375rem;
        text-align: right;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        background-color: white;
    }

    .table-salaires input[type="text"]:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.1);
        outline: none;
        background-color: #fff;
    }

    .table-salaires input[type="text"]:read-only {
        background-color: #e9ecef;
        color: #495057;
        font-weight: 600;
        border-color: #ced4da;
        cursor: not-allowed;
    }

    .table-salaires tbody tr.total-row input {
        background-color: #fff;
        border: 1px solid var(--bs-border-color);
        font-weight: 700;
        color: var(--bs-heading-color);
        font-size: 1rem;
    }

    @media (max-width: 1200px) {
        .table-salaires { font-size: 0.85rem; }
        .table-salaires th, .table-salaires td { padding: 0.5rem; }
        .table-salaires input[type="text"] { padding: 0.4rem; font-size: 0.85rem; }
    }

    @media (max-width: 768px) {
        .table-salaires th { font-size: 0.75rem; padding: 0.5rem 0.25rem; }
        .table-salaires td { padding: 0.5rem 0.25rem; }
        .table-salaires input[type="text"] { padding: 0.3rem; font-size: 0.8rem; }
    }
</style>
