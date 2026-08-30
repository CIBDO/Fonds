@extends('layouts.master')

@section('title', 'Notifications')

@section('content')
<x-vuexy.page-header title="Notifications" subtitle="Vos alertes et messages système" />

@forelse ($notifications as $notification)
    <x-vuexy.card class="mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="fs-6">{{ $notification->data['message'] ?? 'Notification' }}</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('markAsRead', $notification->id) }}" class="btn btn-primary btn-sm">
                    <i class="icon-base ti tabler-check me-1"></i>Marquer comme lu
                </a>
                <a href="{{ route('deleteNotification', $notification->id) }}" class="btn btn-danger btn-sm">
                    <i class="icon-base ti tabler-trash me-1"></i>Supprimer
                </a>
            </div>
        </div>
    </x-vuexy.card>
@empty
    <x-vuexy.alert type="info">Aucune notification pour le moment.</x-vuexy.alert>
@endforelse
@endsection
