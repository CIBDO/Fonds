@props(['type' => 'info', 'icon' => null])

@php
    $alertClass = match($type) {
        'warning' => 'alert-warning',
        'danger', 'error' => 'alert-danger',
        'success' => 'alert-success',
        default => 'alert-info',
    };
    $defaultIcon = match($type) {
        'warning' => 'tabler-alert-triangle',
        'danger', 'error' => 'tabler-alert-circle',
        'success' => 'tabler-circle-check',
        default => 'tabler-info-circle',
    };
@endphp

<div {{ $attributes->merge(['class' => "alert $alertClass d-flex align-items-start gap-2"]) }} role="alert">
    <i class="icon-base ti {{ $icon ?? $defaultIcon }} icon-md flex-shrink-0 mt-1"></i>
    <div>{{ $slot }}</div>
</div>
