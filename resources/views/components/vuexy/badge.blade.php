@props(['type' => 'primary', 'label' => ''])

<span {{ $attributes->merge(['class' => "badge bg-label-$type"]) }}>
    {{ $label ?: $slot }}
</span>
