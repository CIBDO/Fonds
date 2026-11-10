@props([
    'path' => null,
    'downloadRoute' => null,
    'model' => null,
    'showPreview' => true,
])

@php
    $filePath = $path ?? ($model->preuve_paiement ?? null);
    $disk = \Illuminate\Support\Facades\Storage::disk('public');
    $exists = $filePath && $disk->exists($filePath);
    $url = $exists ? $disk->url($filePath) : null;
    $ext = $filePath ? strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) : null;
    $previewable = in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp'], true);
    $downloadUrl = $downloadRoute && $model ? route($downloadRoute, $model) : null;
@endphp

@if($filePath)
<div class="preuve-paiement">
    @if($exists)
    <div class="d-flex flex-wrap gap-2 mb-0">
        @if($downloadUrl)
            <a href="{{ $downloadUrl }}" class="btn btn-sm btn-label-secondary">
                <i class="icon-base ti tabler-download me-1"></i>Télécharger
            </a>
        @elseif($url)
            <a href="{{ $url }}" class="btn btn-sm btn-label-secondary" download>
                <i class="icon-base ti tabler-download me-1"></i>Télécharger
            </a>
        @endif
        @if($showPreview && $url)
            <a href="{{ $url }}" class="btn btn-sm btn-label-primary" target="_blank" rel="noopener">
                <i class="icon-base ti tabler-external-link me-1"></i>Ouvrir
            </a>
        @endif
        <span class="badge bg-label-secondary align-self-center">{{ strtoupper($ext) }} · {{ basename($filePath) }}</span>
    </div>

    @if($showPreview && $previewable && $url)
        @if($ext === 'pdf')
            <iframe
                src="{{ $url }}"
                class="w-100 border rounded mt-3"
                style="min-height: 420px;"
                title="Aperçu preuve de paiement">
            </iframe>
        @else
            <div class="text-center border rounded p-2 bg-label-secondary mt-3">
                <img
                    src="{{ $url }}"
                    alt="Preuve de paiement"
                    class="img-fluid rounded"
                    style="max-height: 480px;">
            </div>
        @endif
    @endif
    @else
        <div class="alert alert-danger mb-0">
            <h6 class="alert-heading fw-semibold mb-2">
                <i class="icon-base ti tabler-alert-triangle me-1"></i>
                Fichier introuvable sur le serveur
            </h6>
            <p class="mb-2 text-body">
                La preuve est référencée dans la base mais le fichier physique
                <code class="text-danger">{{ $filePath }}</code> n'existe pas dans
                <code>storage/app/public/</code>.
            </p>
            <p class="mb-0 small">
                Si vous venez de restaurer une base sans récupérer le dossier
                <code>storage/app/public/preuves-fnl/</code>, réimportez les fichiers ou
                rechargez une nouvelle preuve via le bouton « Corriger » en haut de la fiche.
            </p>
        </div>
    @endif
</div>
@endif
