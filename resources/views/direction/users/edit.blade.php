@extends('layouts.direction')

@section('title', 'Modifier ' . $user->nomComplet)
@section('page_title', 'Modifier un utilisateur')
@section('page_subtitle', 'Modifiez les informations du compte.')

@section('content')

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-[#1C9F93]/10
                            border-2 border-[#1C9F93]/30 flex items-center
                            justify-center font-bold text-[#1C9F93] text-sm">
                        {{ strtoupper(substr($user->prenomUser, 0, 1)) }}
                        {{ strtoupper(substr($user->nomUser, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-[#0F172A]">{{ $user->nomComplet }}</p>
                        <p class="text-xs text-slate-500">{{ $user->email }}</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('direction.users.update', $user) }}" class="p-6 space-y-5">
                @csrf @method('PUT')

                {{-- Nom et Prénom --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nomUser" value="{{ old('nomUser', $user->nomUser) }}"
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
                        <input type="text" name="prenomUser" value="{{ old('prenomUser', $user->prenomUser) }}"
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
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
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
                               focus:border-[#1C9F93] bg-white transition-colors
                               @error('role') border-red-400 @else border-slate-300 @enderror">
                        <option value="direction" {{ old('role', $user->role) === 'direction' ? 'selected' : '' }}>
                            Direction
                        </option>
                        <option value="chef_projet" {{ old('role', $user->role) === 'chef_projet' ? 'selected' : '' }}>
                            Chef de projet
                        </option>
                        <option value="pointeur" {{ old('role', $user->role) === 'pointeur' ? 'selected' : '' }}>
                            Pointeur
                        </option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
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
                        Enregistrer les modifications
                    </button>
                </div>

            </form>
        </div>
    </div>

@endsection
