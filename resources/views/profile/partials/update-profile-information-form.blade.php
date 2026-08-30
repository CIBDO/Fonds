<section>
    <p class="text-body-secondary mb-4">
        Mettez à jour les informations de profil et l'adresse e-mail de votre compte.
    </p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="mb-4">
            <label for="name" class="form-label">Nom</label>
            <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-body-secondary small mb-2">
                        Votre adresse e-mail n'est pas vérifiée.
                    </p>
                    <button form="send-verification" class="btn btn-sm btn-label-primary">
                        <i class="icon-base ti tabler-mail me-1"></i>Renvoyer l'e-mail de vérification
                    </button>
                </div>

                @if (session('status') === 'verification-link-sent')
                    <x-vuexy.alert type="success" class="mt-3">
                        Un nouveau lien de vérification a été envoyé à votre adresse e-mail.
                    </x-vuexy.alert>
                @endif
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-device-floppy me-1"></i>Enregistrer
            </button>
            @if (session('status') === 'profile-updated')
                <span class="text-success small">Enregistré.</span>
            @endif
        </div>
    </form>
</section>
