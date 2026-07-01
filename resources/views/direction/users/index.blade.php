@extends('layouts.direction')

@section('title', 'Utilisateurs')
@section('page_title', 'Gestion des utilisateurs')
@section('page_subtitle', 'Créez et gérez les comptes de votre équipe.')

@section('content')

    {{-- Alerte mot de passe temporaire --}}
    @if (session('mot_de_passe'))
        <div class="bg-[#1C9F93]/10 border border-[#1C9F93]/30 rounded-xl p-5">
            <div class="flex items-start gap-3">
                <div
                    class="w-8 h-8 bg-[#1C9F93] rounded-lg flex items-center
                        justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11
                                     17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0
                                     01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-[#0F172A]">
                        Mot de passe pour {{ session('user_nom') }}
                    </p>
                    <p class="text-sm text-slate-600 mt-1">
                        Mot de passe temporaire :
                        <span
                            class="font-mono font-bold text-[#1C9F93] bg-white
                                 px-2 py-0.5 rounded border border-[#1C9F93]/30">
                            {{ session('mot_de_passe') }}
                        </span>
                    </p>
                    <p class="text-xs text-slate-500 mt-1">
                        Communiquez ce mot de passe à l'utilisateur.
                        Il devra le changer à sa prochaine connexion.
                    </p>
                </div>
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
