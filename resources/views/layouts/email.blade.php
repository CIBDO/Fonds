@extends('layouts.master')

@push('vendor-style')
<link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/css/pages/app-email.css') }}">
@endpush

@section('content')
<div class="app-email card">
    <div class="row g-0">
        <div class="col app-email-sidebar border-end flex-grow-0" id="app-email-sidebar">
            @include('partials.mail_sidebar')
        </div>
        <div class="col @yield('email-column', 'app-emails-list')">
            @yield('email-content')
        </div>
    </div>
</div>

@include('messages.partials.compose_modal')
@endsection
