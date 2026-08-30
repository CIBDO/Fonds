@extends('layouts.master')

@section('title', 'Mon Profil')

@section('content')
<x-vuexy.page-header title="Mon Profil" subtitle="Gérez vos informations personnelles et la sécurité de votre compte" />

<div class="row g-6">
    <div class="col-12">
        <x-vuexy.card title="Informations du profil" icon="tabler-user">
            @include('profile.partials.update-profile-information-form')
        </x-vuexy.card>
    </div>

    <div class="col-12">
        <x-vuexy.card title="Mot de passe" icon="tabler-lock">
            @include('profile.partials.update-password-form')
        </x-vuexy.card>
    </div>

    <div class="col-12">
        <x-vuexy.card title="Supprimer le compte" icon="tabler-trash">
            @include('profile.partials.delete-user-form')
        </x-vuexy.card>
    </div>
</div>
@endsection
