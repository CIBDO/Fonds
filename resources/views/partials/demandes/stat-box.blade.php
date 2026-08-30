@props(['label', 'value', 'col' => 'col-md-3 col-sm-6'])

<div class="{{ $col }}">
    <div class="card h-100">
        <div class="card-body">
            <p class="text-body-secondary small mb-1">{{ $label }}</p>
            <h6 class="mb-0 fw-semibold">{{ $value }}</h6>
        </div>
    </div>
</div>
