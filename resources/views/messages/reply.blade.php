@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-3 mb-4">
            @include('partials.mail_sidebar')
        </div>
        <div class="col-12 col-md-9">
            <!-- Header bleu simple -->
            <div class="reply-header mb-4" style="background: linear-gradient(135deg, #effdf5 0%, #08a551 100%); padding: 20px 24px; border-radius: 16px; color: #0a0a0a;">
                <div class="d-flex align-items-center">
                    <div class="header-icon me-3">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10 9V7.41c0-.89-1.08-1.34-1.71-.71L3.7 11.29c-.39.39-.39 1.02 0 1.41l4.59 4.59c.63.63 1.71.18 1.71-.71V14.9c5 0 8.5 1.6 11 5.1-1-5-4-10-11-11z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="mb-0 fw-bold" style="font-size: 20px;">Répondre au message</h1>
                        <p class="mb-0 opacity-75" style="font-size: 14px;">Envoyez votre réponse à l'expéditeur</p>
                    </div>
                </div>
            </div>

            <!-- Contenu épuré -->
            <div class="reply-content" style="background: #ffffff; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
                @if ($errors->any())
                    <div class="alert alert-danger border-0 m-4" style="background: #FEE2E2; color: #DC2626; border-radius: 8px;">
                        <div class="d-flex align-items-center">
                            <svg class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            <strong>Erreur de validation</strong>
                        </div>
                        <ul class="mb-0 mt-2" style="list-style: none; padding-left: 0;">
                            @foreach ($errors->all() as $error)
                                <li style="display: flex; align-items: center; margin-bottom: 4px;">
                                    <span style="color: #DC2626; margin-right: 8px;">•</span>
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Message original simple -->
                <div class="original-message p-4" style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0;">
                    <div class="message-info mb-3">
                        <h6 class="fw-semibold mb-2" style="color: #374151; font-size: 14px;">
                            <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                            Message original
                        </h6>
                        <div class="d-flex align-items-center mb-2">
                            @php
                                $avatar = $message->sender->avatar ?? null;
                                $initial = strtoupper(substr($message->sender->name ?? 'U', 0, 1));
                                $colors = ['#3B82F6', '#1D4ED8', '#2563EB', '#1E40AF', '#1E3A8A', '#312E81'];
                                $colorIndex = ($message->sender->id ?? 0) % count($colors);
                                $avatarColor = $colors[$colorIndex];
                            @endphp
                            <div style="width: 32px; height: 32px; background: {{ $avatarColor }}; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 500; font-size: 14px; margin-right: 12px;">
                                @if($avatar)
                                    <img src="{{ asset('assets/img/profiles/' . $avatar) }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                @else
                                    {{ $initial }}
                                @endif
                            </div>
                            <div>
                                <div class="fw-semibold" style="color: #111827;">{{ $message->sender->name ?? 'Expéditeur inconnu' }}</div>
                                <div style="color: #6B7280; font-size: 12px;">{{ $message->sender->email ?? '' }}</div>
                            </div>
                        </div>
                        <div style="color: #6B7280; font-size: 13px; margin-bottom: 4px;">
                            <strong>Sujet :</strong> {{ $message->subject }}
                        </div>
                        <div style="color: #6B7280; font-size: 12px;">
                            <strong>Date :</strong> {{ $message->sent_at ? \Carbon\Carbon::parse($message->sent_at)->format('d/m/Y H:i') : '' }}
                        </div>
                    </div>
                </div>

                <!-- Formulaire de réponse -->
                <form action="{{ route('messages.reply', $message->id) }}" method="POST" enctype="multipart/form-data" class="p-4">
                    @csrf

                    <div class="form-group mb-4">
                        <label for="body" class="form-label fw-semibold" style="color: #374151; font-size: 14px; margin-bottom: 8px; display: block;">
                            <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                            </svg>
                            Votre réponse
                        </label>
                        <textarea name="body" id="body" class="form-control" rows="6" required
                                  placeholder="Écrivez votre réponse ici..."
                                  style="border: 2px solid #E5E7EB; border-radius: 12px; padding: 16px; font-size: 14px; background: #F9FAFB; transition: all 0.2s ease;"></textarea>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label fw-semibold" style="color: #374151; font-size: 14px; margin-bottom: 8px; display: block;">
                            <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16.5 6v11.5c0 2.21-1.79 4-4 4s-4-1.79-4-4V5c0-1.38 1.12-2.5 2.5-2.5s2.5 1.12 2.5 2.5v10.5c0 .55-.45 1-1 1s-1-.45-1-1V6H10v9.5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V5c0-2.21-1.79-4-4-4S7 2.79 7 5v12.5c0 3.04 2.46 5.5 5.5 5.5s5.5-2.46 5.5-5.5V6h-1.5z"/>
                            </svg>
                            Pièces jointes
                        </label>
                        <div class="file-input-container">
                            <input type="file" name="attachments[]" id="attachments" class="d-none" multiple>
                            <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('attachments').click()"
                                    style="border: 2px dashed #D1D5DB; background: #F9FAFB; border-radius: 12px; padding: 16px; width: 100%; text-align: center; transition: all 0.2s ease;">
                                <svg class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="#6B7280">
                                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                                </svg>
                                Sélectionner des fichiers
                            </button>
                            <div class="file-info mt-2" style="font-size: 12px; color: #6B7280; text-align: center;">
                                Formats acceptés : JPG, PNG, PDF, DOC, XLS, ZIP... (max 2MB par fichier)
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary"
                           style="border-radius: 12px; padding: 10px 20px; font-size: 14px; font-weight: 500;">
                            <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                            </svg>
                            Retour
                        </a>
                        <button type="submit" class="btn btn-primary"
                                style="background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%); border: none; border-radius: 12px; padding: 10px 24px; font-size: 14px; font-weight: 500;">
                            <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                            </svg>
                            Envoyer la réponse
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Styles épurés -->
<style>
/* Animation d'entrée */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.reply-content {
    animation: fadeInUp 0.3s ease-out;
}

/* Style des champs */
.form-control:focus {
    border-color: #1D4ED8 !important;
    box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1) !important;
}

/* Style des boutons */
.btn-primary:hover {
    background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(29, 78, 216, 0.3);
}

.btn-outline-secondary:hover {
    background: #F3F4F6 !important;
    border-color: #D1D5DB !important;
}

/* Zone de fichier */
.file-input-container button:hover {
    border-color: #1D4ED8 !important;
    background: #EFF6FF !important;
}

/* Responsive */
@media (max-width: 768px) {
    .reply-header {
        padding: 16px 20px;
    }

    .reply-header h1 {
        font-size: 18px;
    }

    .reply-content {
        margin: 0 -16px;
        border-radius: 0;
    }

    .d-flex.justify-content-end {
        flex-direction: column;
    }

    .d-flex.justify-content-end .btn {
        width: 100%;
        margin-bottom: 8px;
    }
}
</style>
@endsection
