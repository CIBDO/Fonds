@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-3 mb-4">
            @include('partials.mail_sidebar')
        </div>
        <div class="col-12 col-md-9">
            <div class="card shadow rounded-4">
                <div class="card-header bg-white border-0 rounded-top-4">
                    <h4 class="mb-0 fw-bold"><i class="fas fa-reply"></i> Répondre au message</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Erreur !</strong> Veuillez corriger les erreurs suivantes :
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <form action="{{ route('messages.reply', $message->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="body" class="form-label"><i class="fas fa-envelope"></i> Corps du message</label>
                            <textarea name="body" class="form-control" rows="5" placeholder="Écrivez votre réponse ici..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="attachments" class="form-label"><i class="fas fa-paperclip"></i> Pièces jointes</label>
                            <input type="file" name="attachments[]" class="form-control" multiple id="attachmentInput">
                            <small class="form-text text-muted">Formats acceptés : jpg, jpeg, png, pdf, doc, xls, zip...</small>
                            <div id="fileList" class="mt-2 text-muted small"></div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary fw-bold px-4">
                                <i class="fas fa-paper-plane"></i> Envoyer la réponse
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-secondary px-4">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('attachmentInput')?.addEventListener('change', function () {
        const fileList = document.getElementById('fileList');
        fileList.innerHTML = '';
        for (let i = 0; i < this.files.length; i++) {
            const file = this.files[i];
            fileList.innerHTML += `<span><i class='fas fa-file'></i> ${file.name}</span><br>`;
        }
    });
</script>
@endsection
