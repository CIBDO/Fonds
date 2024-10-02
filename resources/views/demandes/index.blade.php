@extends('layouts.master')

@section('content')
<div class="container">
    <h2 class="my-4">Liste des Demandes de Fonds</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Mois</th>
                <th>Date de réception</th>
                <th>Poste</th>
                <th>Total demandé (mois courant)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($demandes as $demande)
            <tr>
                <td>{{ $demande->mois }}</td>
                <td>{{ $demande->date_reception }}</td>
                <td>{{ $demande->poste->nom }}</td>
                <td>{{ $demande->total_courant }}</td>
                <td>
                    <a href="{{ route('demandes-fonds.show', $demande->id) }}" class="btn btn-info btn-sm">
                        <i class="fa fa-eye"></i>
                    </a>
                    <a href="{{ route('demandes-fonds.edit', $demande->id) }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-edit"></i>
                    </a>
                    <form action="{{ route('demandes-fonds.destroy', $demande->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
