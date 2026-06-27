<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changement de mot de passe — Dima Groupe</title>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6
                             a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0
                             00-8 0v4h8z" />
                </svg>
            </div>

            <h2 class="text-xl font-semibold text-primary text-center mb-1">
                Bienvenue {{ auth()->user()->prenomUser }} !
            </h2>
            <p class="text-muted text-sm text-center mb-6">
                Pour des raisons de sécurité, veuillez définir
                votre nouveau mot de passe.
            </p>

            {{-- Erreurs --}}
            @if ($errors->any())
                <div
                    class="bg-red-50 border border-red-200 text-red-700
                            rounded-lg p-4 mb-6 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.change.update') }}">
                @csrf

                {{-- Nouveau mot de passe --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nouveau mot de passe
                    </label>
                    <input type="password" name="password" placeholder="Minimum 8 caractères"
                        class="w-full px-4 py-2.5 border rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary
                               @error('password') border-red-400 @else border-gray-300 @enderror">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirmation --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Confirmer le mot de passe
                    </label>
                    <input type="password" name="password_confirmation" placeholder="Répétez le mot de passe"
                        class="w-full px-4 py-2.5 border rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary
                               @error('password_confirmation') border-red-400
                               @else border-gray-300 @enderror">
                </div>

                {{-- Bouton --}}
                <button type="submit"
                    class="w-full bg-primary text-white py-2.5 rounded-lg
                           font-medium hover:bg-accent transition-colors duration-200">
                    Valider et continuer
                </button>

            </form>
        </div>

    </div>

</body>

</html>
