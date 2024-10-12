@extends('layouts.master')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Postes</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item active">Postes</li>
            </ul>
        </div>
    </div>
</div>

<div class="demande-group-form">
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Rechercher par poste ...">
            </div>
        </div>
        <div class="col-lg-2">
            <div class="search-student-btn">
                <button type="btn" class="btn btn-primary">Rechercher</button>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="card card-table">
            <div class="card-body">

                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Postes</h3>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                            <a href="#" class="btn btn-outline-gray me-2 active"><i
                                    class="feather-list"></i></a>
                            <a href="#" class="btn btn-outline-gray me-2"><i
                                    class="feather-grid"></i></a>
                            <a href="#" class="btn btn-outline-primary me-2"><i class="fas fa-download"></i>
                                Download</a>
                            <a href= class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPosteModal"><i class="fas fa-plus"></i></a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped">
                        <thead class="student-thread">
                            <tr>
                                {{-- <th>
                                    <div class="form-check check-tables">
                                        <input class="form-check-input" type="checkbox" value="something">
                                    </div>
                                </th> --}}
                                <th>#</th>
                                <th>Nom du poste</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($postes as $poste)
                            <tr>
                                <td>{{ $poste->id }}</td>
                                <td>{{ $poste->nom }}</td>
                                        <td class="text-end">
                                            <div class="actions">
                                                <a href="{{route('postes.show', $poste->id)}}" class="btn btn-sm bg-success-light me-2">
                                                    <i class="feather-eye"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm bg-danger-light" data-bs-toggle="modal" data-bs-target="#editPosteModal{{ $poste->id }}">
                                                    <i class="feather-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                            </tr>
                            @endforeach   
                          
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@include('postes.add')  
@include('postes.edit')  
@endsection
