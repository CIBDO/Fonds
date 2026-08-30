@props([
    'label' => '',
    'value' => '',
    'subtitle' => '',
    'icon' => 'tabler-chart-bar',
    'href' => '#',
    'variant' => 'primary',
    'iconBg' => 'bg-label-primary',
])

@php
    $borderClass = match($variant) {
        'success', 'funds-requested' => 'border-top border-success border-3',
        'warning', 'customs-receipts' => 'border-top border-warning border-3',
        'danger', 'funds-sent' => 'border-top border-danger border-3',
        'info', 'pending-requests' => 'border-top border-info border-3',
        default => 'border-top border-primary border-3',
    };
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => "card h-100 text-body $borderClass"]) }}>
    <div class="card-body">
        <div class="d-flex align-items-center mb-3">
            <div class="avatar flex-shrink-0 me-3">
                <span class="avatar-initial rounded {{ $iconBg }}">
                    <i class="icon-base ti {{ $icon }} icon-26px"></i>
                </span>
            </div>
            <div>
                <p class="mb-0 text-body-secondary small">{{ $label }}</p>
            </div>
        </div>
        <h4 class="mb-1">{{ $value }}</h4>
        @if($subtitle)
            <small class="text-body-secondary">{{ $subtitle }}</small>
        @endif
    </div>
</a>
