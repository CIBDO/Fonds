@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-7">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0" style="font-family: 'Geologica', sans-serif;">
                    <i class="fas fa-bell"></i> Notifications
                </h2>
            </div>
            @forelse ($notifications as $notification)
                <div class="card shadow-sm rounded-4 mb-3">
                    <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <div class="mb-2 mb-md-0">
                            <span class="fs-5">{{ $notification->data['message'] }}</span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('markAsRead', $notification->id) }}" class="btn btn-primary fw-bold">
                                <i class="fas fa-check"></i> Marquer comme lu
                            </a>
                            <a href="{{ route('deleteNotification', $notification->id) }}" class="btn btn-danger fw-bold">
                                <i class="fas fa-trash"></i> Supprimer
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info text-center">Aucune notification pour le moment.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection