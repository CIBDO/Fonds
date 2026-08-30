@props(['title' => '', 'icon' => null, 'actions' => null])

<div {{ $attributes->merge(['class' => 'card mb-6']) }}>
    @if($title || $icon || $actions || isset($header))
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="card-title mb-0 d-flex align-items-center gap-2">
                @if($icon)
                    <i class="icon-base ti {{ $icon }}"></i>
                @endif
                <h5 class="mb-0">{{ $title }}</h5>
            </div>
            @if($actions)
                <div class="card-actions">{{ $actions }}</div>
            @endif
            @isset($header){{ $header }}@endisset
        </div>
    @endif
    <div class="card-body">
        {{ $slot }}
    </div>
</div>
