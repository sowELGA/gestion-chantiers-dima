<div class="flex items-center justify-between px-6 py-4 hover:bg-slate-50
            transition-colors relative">

    {{-- Infos utilisateur --}}
    <div class="flex items-center gap-4">
        <div
            class="w-10 h-10 rounded-full flex items-center justify-center
                    font-bold text-sm flex-shrink-0
                    {{ $user->actif
                        ? 'bg-[#1C9F93]/10 text-[#1C9F93] border-2 border-[#1C9F93]/30'
                        : 'bg-slate-100 text-slate-400 border-2 border-slate-200' }}">
            {{ strtoupper(substr($user->prenomUser, 0, 1)) }}
            {{ strtoupper(substr($user->nomUser, 0, 1)) }}
        </div>
        <div>
            <p
                class="text-sm font-semibold text-[#0F172A]
                      {{ !$user->actif ? 'opacity-50' : '' }}">
                {{ $user->nomComplet }}
            </p>
            <p class="text-xs text-slate-500">{{ $user->email }}</p>
        </div>
    </div>

    {{-- Badges et actions --}}
    <div class="flex items-center gap-3">

        {{-- Statut actif/inactif --}}
        @if ($user->actif)
            <span
                class="inline-flex items-center gap-1.5 text-xs font-medium
                         text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Actif
            </span>
        @else
            <span
                class="inline-flex items-center gap-1.5 text-xs font-medium
                         text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">
                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                Inactif
            </span>
        @endif

        {{-- Première connexion --}}
        @if ($user->premiere_connexion)
            <span
                class="text-xs font-medium text-amber-700 bg-amber-50
                         px-2.5 py-1 rounded-full">
                Première connexion
            </span>
        @endif

        {{-- Menu actions --}}
        <div x-data="{ open: false, openUp: false }" class="relative">
            <button
                @click="
            const rect = $el.getBoundingClientRect();
            const spaceBelow = window.innerHeight - rect.bottom;
            openUp = spaceBelow < 180;
            open = !open;
        "
                class="p-2 text-slate-400 hover:text-[#0F172A]
               hover:bg-slate-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0
                     010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2
                     1 1 0 010 2z" />
                </svg>
            </button>

            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95" :class="openUp ? 'bottom-full mb-2' : 'top-full mt-2'"
                class="absolute right-0 w-52 bg-white rounded-xl shadow-xl
                border border-slate-200 py-1 z-50">

                {{-- Modifier --}}
                <a href="{{ route('direction.users.edit', $user) }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm
                  text-slate-600 hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0
                         002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828
                         15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </a>

                {{-- Réinitialiser mot de passe --}}
                <form method="POST" action="{{ route('direction.users.reinitialiser', $user) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                        class="w-full flex items-center gap-2 px-4 py-2.5 text-sm
                           text-slate-600 hover:bg-slate-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11
                             17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0
                             01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        Réinitialiser mot de passe
                    </button>
                </form>

                <div class="border-t border-slate-100 my-1"></div>

                {{-- Activer / Désactiver --}}
                <form method="POST" action="{{ route('direction.users.toggle-statut', $user) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                        class="w-full flex items-center gap-2 px-4 py-2.5 text-sm
                           transition-colors
                           {{ $user->actif ? 'text-red-500 hover:bg-red-50' : 'text-emerald-600 hover:bg-emerald-50' }}">
                        @if ($user->actif)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728
                                 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            Désactiver le compte
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Activer le compte
                        @endif
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>
