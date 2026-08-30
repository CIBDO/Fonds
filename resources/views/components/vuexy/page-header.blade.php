@props(['title' => '', 'subtitle' => null])

<div class="d-flex flex-wrap justify-content-between align-items-center mb-6 gap-3">
    <div>
        <h4 class="mb-1">{{ $title }}</h4>
        @if($subtitle)
            <p class="text-body-secondary mb-0">{{ $subtitle }}</p>
        @endif
    </div>
    @if(isset($actions))
        <div class="d-flex gap-2 flex-wrap">{{ $actions }}</div>
    @endif
</div>
