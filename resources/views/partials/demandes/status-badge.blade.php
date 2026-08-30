@props(['status'])

@if($status === 'approuve')
    <span class="badge bg-label-success">
        <i class="icon-base ti tabler-circle-check me-1"></i>Approuvé
    </span>
@elseif($status === 'rejete')
    <span class="badge bg-label-danger">
        <i class="icon-base ti tabler-circle-x me-1"></i>Rejeté
    </span>
@else
    <span class="badge bg-label-warning">
        <i class="icon-base ti tabler-clock me-1"></i>En attente
    </span>
@endif
