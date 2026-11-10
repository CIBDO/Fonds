@push('vendor-style')
<link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}">
@endpush

@push('vendor-script')
{{-- Bundle Vuexy : DataTables + Responsive + Buttons + JSZip + pdfMake --}}
<script src="{{ asset('vuexy/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="{{ asset('assets/js/datatables-fr.js') }}"></script>
@endpush
