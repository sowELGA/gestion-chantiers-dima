@extends('layouts.direction')

@section('title', 'Nouvel utilisateur')
@section('page_title', 'Nouvel utilisateur')
@section('page_subtitle', 'Créer un nouveau compte pour votre équipe.')

@section('content')

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="font-semibold text-[#0F172A]">
                    Informations du compte
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Un mot de passe temporaire sera généré automatiquement.
                </p>
            </div>

            <form method="POST" action="{{ route('direction.users.store') }}" class="p-6 space-y-5">
                @csrf

                {{-- Nom et Prénom --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nomUser" value="{{ old('nomUser') }}" placeholder="Ex : Diaw"
                            class="w-full px-4 py-2.5 border rounded-lg text-sm
                                  focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                                  focus:border-[#1C9F93] transition-colors
                                  @error('nomUser') border-red-400 bg-red-50
                                  @else border-slate-300 @enderror">
                        @error('nomUser')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Prénom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="prenomUser" value="{{ old('prenomUser') }}" placeholder="Ex : Mamadou"
                            class="w-full px-4 py-2.5 border rounded-lg text-sm
                                  focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                                  focus:border-[#1C9F93] transition-colors
                                  @error('prenomUser') border-red-400 bg-red-50
                                  @else border-slate-300 @enderror">
                        @error('prenomUser')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="exemple@dimagroupe.com"
                        class="w-full px-4 py-2.5 border rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                              focus:border-[#1C9F93] transition-colors
                              @error('email') border-red-400 bg-red-50
                              @else border-slate-300 @enderror">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Rôle --}}
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Rôle <span class="text-red-500">*</span>
                    </label>
                    <select name="role"
                        class="w-full px-4 py-2.5 border rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                               focus:border-[#1C9F93] transition-colors bg-white
                               @error('role') border-red-400 bg-red-50
                               @else border-slate-300 @enderror">
                        <option value="">Sélectionner un rôle</option>
                        <option value="direction" {{ old('role') === 'direction' ? 'selected' : '' }}>
                            Direction
                        </option>
                        <option value="chef_projet" {{ old('role') === 'chef_projet' ? 'selected' : '' }}>
                            Chef de projet
                        </option>
                        <option value="pointeur" {{ old('role') === 'pointeur' ? 'selected' : '' }}>
                            Pointeur
                        </option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Info mot de passe --}}
                <div
                    class="flex items-start gap-3 bg-amber-50 border border-amber-200
                        rounded-lg px-4 py-3">
                    <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-xs text-amber-700">
                        Un mot de passe temporaire sera généré automatiquement et
                        affiché après la création. L'utilisateur devra le modifier
                        à sa première connexion.
                    </p>
                </div>

                {{-- Boutons --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('direction.users.index') }}"
                        class="px-4 py-2.5 text-sm font-medium text-slate-600
                          hover:bg-slate-100 rounded-lg transition-colors">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-[#1C9F93] text-white text-sm
                               font-medium rounded-lg hover:bg-[#178a7f]
                               transition-colors">
                        Créer le compte
                    </button>
                </div>

            </form>
        </div>
    </div>

@endsection
