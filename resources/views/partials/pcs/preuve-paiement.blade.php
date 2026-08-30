@props([
    'path' => null,
    'downloadRoute' => null,
    'model' => null,
    'showPreview' => true,
])

@php
    $filePath = $path ?? ($model->preuve_paiement ?? null);
    $exists = $filePath && \Illuminate\Support\Facades\Storage::disk('public')->exists($filePath);
    $url = $exists ? \Illuminate\Support\Facades\Storage::disk('public')->url($filePath) : null;
    $ext = $filePath ? strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) : null;
    $previewable = in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp'], true);
@endphp

@if($exists)
<div class="preuve-paiement">
    <div class="d-flex flex-wrap gap-2 mb-3">
        @if($downloadRoute && $model)
            <a href="{{ route($downloadRoute, $model) }}" class="btn btn-sm btn-label-secondary">
                <i class="icon-base ti tabler-download me-1"></i>Télécharger
            </a>
        @endif
        @if($url)
            <a href="{{ $url }}" class="btn btn-sm btn-label-primary" target="_blank" rel="noopener">
                <i class="icon-base ti tabler-external-link me-1"></i>Ouvrir
            </a>
        @endif
        <span class="badge bg-label-secondary align-self-center">{{ strtoupper($ext) }} · {{ basename($filePath) }}</span>
    </div>

    @if($showPreview && $previewable && $url)
        @if($ext === 'pdf')
            <iframe src="{{ $url }}" class="w-100 border rounded" style="min-height: 420px;" title="Aperçu preuve de paiement"></iframe>
        @else
            <div class="text-center border rounded p-2 bg-label-secondary">
                <img src="{{ $url }}" alt="Preuve de paiement" class="img-fluid rounded" style="max-height: 480px;">
            </div>
        @endif
    @elseif($showPreview)
        <p class="text-body-secondary small mb-0">
            <i class="icon-base ti tabler-info-circle me-1"></i>
            Aperçu intégré indisponible pour ce format. Utilisez le téléchargement ou l'ouverture dans un nouvel onglet.
        </p>
    @endif
</div>
@endif
