<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Dima Groupe</title>
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

        /* Blueprint grid — minor + major rule lines, like a technical drawing sheet */
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

        /* Registration ticks — corner marks like alignment crops on a printed plan */
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

        /* Dimension-style divider under headings */
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

        /* Crane / structure line-art draw-in on load */
        .rig-path {
            stroke-dasharray: 900;
            stroke-dashoffset: 900;
            animation: draw 2.4s ease-out forwards .2s;
        }

        @media (prefers-reduced-motion: reduce) {
            .rig-path {
                animation: none;
                stroke-dashoffset: 0;
            }
        }

        @keyframes draw {
            to {
                stroke-dashoffset: 0;
            }
        }
    </style>
</head>

<body class="bg-lightbg min-h-screen overflow-hidden">

    <div class="lg:grid lg:grid-cols-2 min-h-screen">

        {{-- Panneau plan / signature visuelle — masqué en mobile --}}
        <div class="hidden lg:flex blueprint-grid relative flex-col justify-between overflow-hidden p-12 text-white">

            <div class="flex items-center justify-between font-mono-tag text-[11px] tracking-widest text-teal-100/60">
                <span>DIMA GROUPE — PLATEFORME GESTION ET SUIVIE DES CHANTIERS</span>
                <span>ÉCH. 1:1</span>
            </div>

            <div class="relative">
                <svg viewBox="0 0 420 260" class="absolute -right-6 bottom-24 w-72 opacity-90" fill="none">
                    <path class="rig-path" d="M40 240 L40 40 L230 40 L230 70" stroke="#e2faf5" stroke-width="2"
                        stroke-linecap="round" />
                    <path class="rig-path" d="M40 40 L20 55" stroke="#e2faf5" stroke-width="2" stroke-linecap="round" />
                    <path class="rig-path" d="M230 70 L230 240" stroke="#e2faf5" stroke-width="2" stroke-opacity=".5"
                        stroke-dasharray="4 5" />
                    <path class="rig-path" d="M70 240 L70 120 L170 120 L170 240" stroke="#e2faf5" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" />
                    <path class="rig-path" d="M70 160 L170 200 M70 200 L170 160" stroke="#e2faf5" stroke-width="1.5"
                        stroke-opacity=".6" />
                    <path class="rig-path" d="M95 120 L95 90 L145 90 L145 120" stroke="#e2faf5" stroke-width="2" />
                    <circle class="rig-path" cx="230" cy="70" r="4" stroke="#e2faf5" stroke-width="2" />
                </svg>

                <p class="font-mono-tag text-[11px] tracking-widest text-teal-100/60 mb-4">ACCÈS PLATEFORME</p>
                <h1 class="font-display text-4xl font-semibold leading-tight max-w-sm">
                    Le meilleur<br>est à construire.
                </h1>
                <div class="dim-rule w-40 mt-6 mb-6"></div>
                <p class="text-teal-50/70 text-sm max-w-xs leading-relaxed">
                    Suivi d'avancement, ressources et planning de chantier,
                    réunis dans un seul espace.
                </p>
            </div>

            <div class="flex items-center justify-between font-mono-tag text-[11px] tracking-widest text-teal-100/50">
                <span>© {{ date('Y') }} DIMA GROUPE</span>
                <span>DWG—LOGIN—01</span>
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

                <p class="font-mono-tag text-[11px] tracking-widest text-primary/70 mb-2">ESPACE CHANTIER</p>
                <h2 class="font-display text-2xl font-semibold text-primary mb-1">
                    Connexion
                </h2>
                <p class="text-muted text-sm mb-8">
                    Accédez à vos chantiers et suivez leur avancement en temps réel.
                </p>

                {{-- Carte --}}
                <div
                    class="plan-card bg-white rounded-lg shadow-[0_1px_2px_rgba(16,24,28,.04),0_12px_32px_-16px_rgba(16,24,28,.18)] border border-slate-200 p-8">
                    <span class="tick tick-tl"></span>
                    <span class="tick tick-tr"></span>
                    <span class="tick tick-bl"></span>
                    <span class="tick tick-br"></span>

                    {{-- Erreurs globales --}}
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 rounded-md p-4 mb-6 text-sm">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        {{-- Email --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                Email
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                placeholder="exemple@dimagroupe.com"
                                class="w-full px-4 py-2.5 border rounded-md text-sm
                                       transition-shadow duration-150
                                       focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent
                                       @error('email') border-red-400 @else border-gray-300 @enderror">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Mot de passe --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                Mot de passe
                            </label>
                            <input type="password" name="password" placeholder="••••••••"
                                class="w-full px-4 py-2.5 border rounded-md text-sm
                                       transition-shadow duration-150
                                       focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent
                                       @error('password') border-red-400 @else border-gray-300 @enderror">
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Bouton --}}
                        <button type="submit"
                            class="w-full bg-primary text-white py-2.5 rounded-md
                                   font-medium text-sm tracking-wide
                                   hover:bg-accent active:scale-[.99]
                                   transition-all duration-150">
                            Se connecter
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
