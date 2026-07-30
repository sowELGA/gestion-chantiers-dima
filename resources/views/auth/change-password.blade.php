<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changement de mot de passe — Dima Groupe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --dg-primary: #0f9c8a;
            --dg-accent: #0b7d6f;
            --dg-ink: #16292b;
            --dg-blueprint: #0c2b29;
            --dg-blueprint-line: rgba(226, 250, 245, .5);
        }

        .font-display {
            font-family: 'Space Grotesk', ui-sans-serif, system-ui, sans-serif;
        }

        .font-mono-tag {
            font-family: 'JetBrains Mono', ui-monospace, monospace;
        }

        .blueprint-grid {
            background-color: var(--dg-blueprint);
            background-image:
                linear-gradient(var(--dg-blueprint-line) 1px, transparent 1px),
                linear-gradient(90deg, var(--dg-blueprint-line) 1px, transparent 1px),
                linear-gradient(rgba(226, 250, 245, .16) 1px, transparent 1px),
                linear-gradient(90deg, rgba(226, 250, 245, .16) 1px, transparent 1px);
            background-size: 96px 96px, 96px 96px, 24px 24px, 24px 24px;
            background-position: -1px -1px;
        }

        .plan-card {
            position: relative;
        }

        .plan-card .tick {
            position: absolute;
            width: 18px;
            height: 18px;
            pointer-events: none;
        }

        .plan-card .tick-tl {
            top: -1px;
            left: -1px;
            border-top: 2px solid var(--dg-primary);
            border-left: 2px solid var(--dg-primary);
        }

        .plan-card .tick-tr {
            top: -1px;
            right: -1px;
            border-top: 2px solid var(--dg-primary);
            border-right: 2px solid var(--dg-primary);
        }

        .plan-card .tick-bl {
            bottom: -1px;
            left: -1px;
            border-bottom: 2px solid var(--dg-primary);
            border-left: 2px solid var(--dg-primary);
        }

        .plan-card .tick-br {
            bottom: -1px;
            right: -1px;
            border-bottom: 2px solid var(--dg-primary);
            border-right: 2px solid var(--dg-primary);
        }

        .dim-rule {
            position: relative;
            height: 1px;
            background: rgba(15, 156, 138, .35);
        }

        .dim-rule::before,
        .dim-rule::after {
            content: '';
            position: absolute;
            top: -3px;
            width: 1px;
            height: 7px;
            background: rgba(15, 156, 138, .55);
        }

        .dim-rule::before {
            left: 0;
        }

        .dim-rule::after {
            right: 0;
        }

        /* Niveau à bulle stylisé — précision avant de continuer */
        .level-path {
            stroke-dasharray: 620;
            stroke-dashoffset: 620;
            animation: draw 1.8s ease-out forwards .15s;
        }

        .level-bubble {
            transform-origin: 210px 90px;
            animation: settle 1.6s ease-out forwards .4s;
        }

        @media (prefers-reduced-motion: reduce) {
            .level-path {
                animation: none;
                stroke-dashoffset: 0;
            }

            .level-bubble {
                animation: none;
            }
        }

        @keyframes draw {
            to {
                stroke-dashoffset: 0;
            }
        }

        @keyframes settle {
            0% {
                transform: translateX(-14px);
            }

            60% {
                transform: translateX(4px);
            }

            100% {
                transform: translateX(0);
            }
        }
    </style>
</head>

<body class="bg-lightbg min-h-screen">

    <div class="lg:grid lg:grid-cols-2 min-h-screen">

        {{-- Panneau plan / signature visuelle — masqué en mobile --}}
        <div class="hidden lg:flex blueprint-grid relative flex-col justify-between overflow-hidden p-12 text-white">

            <div class="flex items-center justify-between font-mono-tag text-[11px] tracking-widest text-teal-100/60">
                <span>DIMA GROUPE — PLATEFORME GESTION ET SUIVIE DES CHANTIERS</span>
                <span>ÉCH. 1:1</span>
            </div>

            <div class="relative">
                <svg viewBox="0 0 420 180" class="absolute -right-4 bottom-24 w-72 opacity-90" fill="none">
                    <path class="level-path" d="M60 90 L360 90" stroke="#e2faf5" stroke-width="3"
                        stroke-linecap="round" />
                    <path class="level-path" d="M60 70 L60 110 M360 70 L360 110" stroke="#e2faf5" stroke-width="3"
                        stroke-linecap="round" />
                    <path class="level-path" d="M180 70 L180 110 M240 70 L240 110" stroke="#e2faf5" stroke-width="1.5"
                        stroke-opacity=".5" />
                    <g class="level-bubble">
                        <rect x="180" y="72" width="60" height="36" rx="8" stroke="#e2faf5"
                            stroke-width="2" />
                        <circle cx="210" cy="90" r="9" fill="#e2faf5" fill-opacity=".85" />
                    </g>
                </svg>

                <p class="font-mono-tag text-[11px] tracking-widest text-teal-100/60 mb-4">SÉCURITÉ DU COMPTE</p>
                <h1 class="font-display text-4xl font-semibold leading-tight max-w-sm">
                    Le meilleur<br>est à construire.
                </h1>
                <div class="dim-rule w-40 mt-6 mb-6"></div>
                <p class="text-teal-50/70 text-sm max-w-xs leading-relaxed">
                    Une base solide commence par un accès sécurisé.
                    Définissez votre nouveau mot de passe pour continuer.
                </p>
            </div>

            <div class="flex items-center justify-between font-mono-tag text-[11px] tracking-widest text-teal-100/50">
                <span>© {{ date('Y') }} DIMA GROUPE</span>
                <span>DWG—SECURITE—02</span>
            </div>
        </div>

        {{-- Panneau formulaire --}}
        <div class="flex flex-col justify-center px-6 py-14 sm:px-10 lg:px-20">

            <div class="mx-auto w-full max-w-sm">

                {{-- En-tête compact, visible uniquement en mobile --}}
                <div class="mb-10 text-center lg:hidden">
                    <p class="font-display text-2xl font-bold text-primary">Dima Groupe</p>
                    <p class="text-muted text-sm mt-1">Le meilleur est à construire</p>
                </div>

                <p class="font-mono-tag text-[11px] tracking-widest text-primary/70 mb-2">DERNIÈRE ÉTAPE</p>

                {{-- Carte --}}
                <div
                    class="plan-card bg-white rounded-lg shadow-[0_1px_2px_rgba(16,24,28,.04),0_12px_32px_-16px_rgba(16,24,28,.18)] border border-slate-200 p-8">
                    <span class="tick tick-tl"></span>
                    <span class="tick tick-tr"></span>
                    <span class="tick tick-bl"></span>
                    <span class="tick tick-br"></span>

                    {{-- Icône --}}
                    <div class="flex items-center justify-center w-14 h-14 bg-lightbg rounded-full mx-auto mb-4">
                        <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6
                                     a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0
                                     00-8 0v4h8z" />
                        </svg>
                    </div>

                    <h2 class="font-display text-xl font-semibold text-primary text-center mb-1">
                        Bienvenue {{ auth()->user()->prenomUser }} !
                    </h2>
                    <p class="text-muted text-sm text-center mb-6 leading-relaxed">
                        Pour des raisons de sécurité, veuillez définir
                        votre nouveau mot de passe.
                    </p>

                    {{-- Erreurs --}}
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 rounded-md p-4 mb-6 text-sm">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.change.update') }}">
                        @csrf

                        {{-- Nouveau mot de passe --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                Nouveau mot de passe
                            </label>
                            <input type="password" name="password" placeholder="Minimum 8 caractères"
                                class="w-full px-4 py-2.5 border rounded-md text-sm
                                       transition-shadow duration-150
                                       focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent
                                       @error('password') border-red-400 @else border-gray-300 @enderror">
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirmation --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                Confirmer le mot de passe
                            </label>
                            <input type="password" name="password_confirmation" placeholder="Répétez le mot de passe"
                                class="w-full px-4 py-2.5 border rounded-md text-sm
                                       transition-shadow duration-150
                                       focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent
                                       @error('password_confirmation') border-red-400
                                       @else border-gray-300 @enderror">
                        </div>

                        {{-- Bouton --}}
                        <button type="submit"
                            class="w-full bg-primary text-white py-2.5 rounded-md
                                   font-medium text-sm tracking-wide
                                   hover:bg-accent active:scale-[.99]
                                   transition-all duration-150">
                            Valider et continuer
                        </button>

                    </form>
                </div>

                <p class="text-center text-muted text-xs mt-8 font-mono-tag tracking-wide">
                    © {{ date('Y') }} DIMA GROUPE — TOUS DROITS RÉSERVÉS
                </p>
            </div>
        </div>
    </div>

</body>

</html>
