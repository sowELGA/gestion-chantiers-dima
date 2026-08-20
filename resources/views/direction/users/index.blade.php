@extends('layouts.direction')

@section('title', 'Utilisateurs')
@section('page_title', 'Gestion des utilisateurs')
@section('page_subtitle', 'Créez et gérez les comptes de votre équipe.')

@section('content')

    {{-- Alerte compte créé --}}
    @if (session('compte_cree'))
        @php $info = session('compte_cree'); @endphp
        <div class="bg-[#1C9F93]/10 border border-[#1C9F93]/30 rounded-xl p-5">
            <div class="flex items-start gap-3">
                <div
                    class="w-8 h-8 bg-[#1C9F93] rounded-lg flex items-center
                        justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-[#0F172A] text-sm">
                        Compte créé — {{ $info['nom'] }}
                    </p>
                    @if ($info['email_envoye'])
                        <p class="text-sm text-slate-600 mt-1">
                            ✅ Un email avec les identifiants a été envoyé à
                            <strong>{{ $info['email'] }}</strong>.
                        </p>
                    @else
                        <p class="text-sm text-amber-600 mt-1">
                            ⚠️ L'email n'a pas pu être envoyé. Communiquez
                            manuellement les identifiants :
                        </p>
                        <div
                            class="mt-2 bg-white border border-slate-200 rounded-lg
                                p-3 flex items-center gap-3">
                            <div>
                                <p class="text-xs text-slate-400">Email</p>
                                <p class="text-sm font-medium">{{ $info['email'] }}</p>
                            </div>
                            <div class="border-l border-slate-200 pl-3">
                                <p class="text-xs text-slate-400">Mot de passe</p>
                                <p class="text-sm font-bold font-mono text-[#1C9F93]">
                                    {{ $info['mot_passe'] }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Alerte mot de passe réinitialisé --}}
    @if (session('mdp_reinitialise'))
        @php $info = session('mdp_reinitialise'); @endphp
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
            <div class="flex items-start gap-3">
                <div
                    class="w-8 h-8 bg-amber-500 rounded-lg flex items-center
                        justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4
                                 a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0
                                 1121 9z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-[#0F172A] text-sm">
                        Mot de passe réinitialisé — {{ $info['nom'] }}
                    </p>
                    @if ($info['email_envoye'])
                        <p class="text-sm text-slate-600 mt-1">
                            ✅ Un email avec le nouveau mot de passe a été envoyé à
                            <strong>{{ $info['email'] }}</strong>.
                        </p>
                    @else
                        <div
                            class="mt-2 bg-white border border-slate-200 rounded-lg
                                p-3 flex items-center gap-3">
                            <div class="border-l border-slate-200 pl-3">
                                <p class="text-xs text-slate-400">Nouveau mot de passe</p>
                                <p class="text-sm font-bold font-mono text-amber-600">
                                    {{ $info['mot_passe'] }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Demandes de réinitialisation en attente --}}
    @if ($demandesReset->isNotEmpty())
        <div class="bg-red-50 border border-red-200 rounded-xl p-5">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002
                             6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388
                             6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3
                             0 11-6 0v-1m6 0H9" />
                </svg>
                <p class="font-semibold text-red-700 text-sm">
                    {{ $demandesReset->count() }} demande(s) de réinitialisation en attente
                </p>
            </div>
            <div class="space-y-2">
                @foreach ($demandesReset as $demande)
                    @php $user = $demande->user; @endphp
                    @if ($user)
                        <div
                            class="bg-white border border-red-100 rounded-lg px-4 py-3
                                flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-[#0F172A]">
                                    {{ $user->prenomUser }} {{ $user->nomUser }}
                                </p>
                                <p class="text-xs text-slate-400">
                                    {{ $user->email }}
                                    · {{ $demande->created_at->locale('fr')->isoFormat('D MMM à HH:mm') }}
                                </p>
                            </div>
                            <form method="POST" action="{{ route('direction.users.reinitialiser', $user->idUser) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="px-4 py-2 bg-red-500 text-white text-xs
                                           font-medium rounded-lg hover:bg-red-600
                                           transition-colors">
                                    Réinitialiser et envoyer
                                </button>
                            </form>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-slate-500">
                {{ $users->count() }}
                utilisateur(s) au total
            </p>
        </div>
        <a href="{{ route('direction.users.create') }}"
            class="flex items-center gap-2 bg-[#1C9F93] text-white px-4 py-2.5
              rounded-lg text-sm font-medium hover:bg-[#178a7f] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nouvel utilisateur
        </a>
    </div>

    {{-- Direction --}}
    @if (isset($users['direction']))
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-visible">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#D4AF37]"></span>
                <h3 class="font-semibold text-[#0F172A]">Direction</h3>
                <span class="text-xs text-slate-400 ml-1">
                    ({{ $users['direction']->count() }})
                </span>
            </div>
            <div class="divide-y divide-slate-50">
                @foreach ($users['direction'] as $user)
                    @include('direction.users._row', ['user' => $user])
                @endforeach
            </div>
        </div>
    @endif

    {{-- Chefs de projet --}}
    @if (isset($users['chef_projet']))
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-visible">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#1C9F93]"></span>
                <h3 class="font-semibold text-[#0F172A]">Chefs de projet</h3>
                <span class="text-xs text-slate-400 ml-1">
                    ({{ $users['chef_projet']->count() }})
                </span>
            </div>
            <div class="divide-y divide-slate-50">
                @foreach ($users['chef_projet'] as $user)
                    @include('direction.users._row', ['user' => $user])
                @endforeach
            </div>
        </div>
    @endif

    {{-- Pointeurs --}}
    @if (isset($users['pointeur']))
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-visible">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                <h3 class="font-semibold text-[#0F172A]">Pointeurs</h3>
                <span class="text-xs text-slate-400 ml-1">
                    ({{ $users['pointeur']->count() }})
                </span>
            </div>
            <div class="divide-y divide-slate-50">
                @foreach ($users['pointeur'] as $user)
                    @include('direction.users._row', ['user' => $user])
                @endforeach
            </div>
        </div>
    @endif

    @if ($users->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <p class="text-slate-400 text-sm">Aucun utilisateur pour le moment.</p>
            <a href="{{ route('direction.users.create') }}"
                class="inline-flex items-center gap-2 mt-4 bg-[#1C9F93] text-white
                  px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#178a7f]">
                Créer le premier utilisateur
            </a>
        </div>
    @endif

@endsection
