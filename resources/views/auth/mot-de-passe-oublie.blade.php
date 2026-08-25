<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié — Dima Groupe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-lightbg min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-primary">Dima Groupe</h1>
            <p class="text-muted mt-1">Gestion des chantiers de construction</p>
        </div>

        {{-- Carte --}}
        <div class="bg-white rounded-2xl shadow-lg p-8">

            {{-- Icône --}}
            <div
                class="flex items-center justify-center w-14 h-14 bg-lightbg
                        rounded-full mx-auto mb-4">
                <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11
                             17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707
                             l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>

            {{-- Titre --}}
            <h2 class="text-xl font-semibold text-primary text-center mb-1">
                Mot de passe oublié
            </h2>

            @if (session('success'))

                {{-- ── État succès ── --}}
                <p class="text-muted text-sm text-center mb-6">
                    Votre demande a bien été envoyée.
                </p>

                <div class="bg-green-50 border border-green-200 rounded-xl p-5 mb-6">
                    <div class="flex items-start gap-3">
                        <div
                            class="w-8 h-8 bg-green-100 rounded-full flex items-center
                                    justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-green-700">
                                Demande envoyée avec succès
                            </p>
                            <p class="text-sm text-green-600 mt-1">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Étapes --}}
                <div class="space-y-3 mb-6">
                    @foreach ([['✅', 'Votre demande est transmise à la direction.'], ['⏳', 'La direction réinitialise votre mot de passe.'], ['📧', 'Vous recevez un email avec vos nouveaux identifiants.'], ['🔑', 'Connectez-vous avec vos nouveaux identifiants.']] as [$icon, $texte])
                        <div class="flex items-center gap-3">
                            <span class="text-base flex-shrink-0">{{ $icon }}</span>
                            <p class="text-sm text-gray-600">{{ $texte }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- ── Formulaire ── --}}
                <p class="text-muted text-sm text-center mb-6">
                    Saisissez votre email. Votre demande sera transmise
                    à la direction qui vous enverra un nouveau mot de passe.
                </p>

                {{-- Erreurs --}}
                @if ($errors->any())
                    <div
                        class="bg-red-50 border border-red-200 text-red-700
                                rounded-lg p-4 mb-6 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.oublie.signaler') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Email
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            placeholder="exemple@dimagroupe.com" autofocus
                            class="w-full px-4 py-2.5 border rounded-lg text-sm
                                      focus:outline-none focus:ring-2 focus:ring-primary
                                      @error('email') border-red-400
                                      @else border-gray-300 @enderror">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Info --}}
                    <div
                        class="bg-lightbg rounded-lg px-4 py-3 mb-6 text-xs
                                text-muted flex items-start gap-2">
                        <svg class="w-4 h-4 text-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18
                                     0 9 9 0 0118 0z" />
                        </svg>
                        La direction sera notifiée et vous enverra un email
                        avec vos nouveaux identifiants de connexion.
                    </div>

                    {{-- Bouton --}}
                    <button type="submit"
                        class="w-full bg-primary text-white py-2.5 rounded-lg
                                   font-medium hover:bg-accent transition-colors
                                   duration-200">
                        Envoyer la demande
                    </button>
                </form>

            @endif

            {{-- Retour connexion --}}
            <a href="{{ route('login') }}"
                class="flex items-center justify-center gap-1.5 mt-5 text-sm
                      text-muted hover:text-primary transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour à la connexion
            </a>

        </div>

        {{-- Footer --}}
        <p class="text-center text-muted text-xs mt-6">
            © {{ date('Y') }} Dima Groupe — Tous droits réservés
        </p>

    </div>

</body>

</html>
