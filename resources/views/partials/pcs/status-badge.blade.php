@props(['statut', 'partiel' => false])

@switch($statut)
    @case('brouillon')
        <span class="badge bg-label-secondary"><i class="icon-base ti tabler-pencil me-1"></i>Brouillon</span>
        @break
    @case('soumis')
        @if($partiel)
            <span class="badge bg-label-warning"><i class="icon-base ti tabler-clock me-1"></i>Partiellement validé</span>
        @else
            <span class="badge bg-label-primary"><i class="icon-base ti tabler-send me-1"></i>Soumis</span>
        @endif
        @break
    @case('valide')
        <span class="badge bg-label-success"><i class="icon-base ti tabler-circle-check me-1"></i>Validé</span>
        @break
    @case('rejete')
        <span class="badge bg-label-danger"><i class="icon-base ti tabler-circle-x me-1"></i>Rejeté</span>
        @break
    @case('annule')
        <span class="badge bg-label-danger"><i class="icon-base ti tabler-ban me-1"></i>Annulé</span>
        @break
    @default
        <span class="badge bg-label-secondary">{{ ucfirst($statut) }}</span>
@endswitch
