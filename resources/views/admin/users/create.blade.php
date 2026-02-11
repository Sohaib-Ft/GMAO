@extends('layouts.app')

@section('title', 'Ajouter un utilisateur')
@section('header', 'Ajouter un nouvel utilisateur')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow border border-gray-100">
    @if(session('status'))
            <div role="alert" data-flash class="mb-4 text-green-600 font-medium flex items-center p-4 bg-green-50 rounded-xl">
                <i class='bx bx-check-circle mr-2 text-xl'></i>
                {{ session('status') }}
            </div>
        @endif

    <div class="mb-6 flex items-center justify-between">
        <h3 class="text-xl font-bold text-gray-800">Ajouter un utilisateur</h3>
        <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-blue-100 text-blue-600">
            <i class='bx bx-user-plus text-2xl'></i>
        </div>
    </div>

    <form method="POST" action="{{ route('users.store') }}">
        @csrf

        <div class="mb-5 text-gray-400 uppercase text-xs font-bold tracking-widest border-b pb-2 mb-4">Informations Générales</div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Nom complet</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class='bx bx-user'></i>
                    </span>
                    <input type="text" name="name" value="{{ old('name') }}" 
                        placeholder="Saisir le nom complet"
                        class="pl-10 block w-full rounded-xl border-gray-300 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
                </div>
                @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class='bx bx-envelope'></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email') }}" 
                        placeholder="exemple@mibtech.com"
                        class="pl-10 block w-full rounded-xl border-gray-300 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
                </div>
                @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-bold text-gray-700 mb-2">Rôle</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class='bx bx-shield-quarter'></i>
                </span>
                <select name="role" class="pl-10 block w-full rounded-xl border-gray-300 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
                    <option value="" disabled selected>Sélectionner un rôle</option>
                    <option value="technicien" {{ old('role')=='technicien'?'selected':'' }}>Technicien de maintenance</option>
                    <option value="employe" {{ old('role')=='employe'?'selected':'' }}>Employe</option>
                    <option value="admin" {{ old('role')=='admin'?'selected':'' }}>Administrateur</option>
                </select>
            </div>
            @error('role') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mt-8 mb-5 text-gray-400 uppercase text-xs font-bold tracking-widest border-b pb-2">Sécurité</div>

        <div class="mb-4">
            <label class="block text-sm font-bold text-gray-700 mb-2">Mot de passe temporaire</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class='bx bx-lock-alt'></i>
                </span>
                <input type="password" name="password" placeholder="Min. 8 caractères"
                    class="pl-10 block w-full rounded-xl border-gray-300 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
            </div>
            @error('password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-100 space-x-3">
            <a href="{{ route('users.index') }}" class="px-6 py-2 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-8 py-2.5 bg-[#1e293b] text-white rounded-xl font-bold shadow-lg hover:bg-gray-800 transition transform hover:-translate-y-0.5">
                Créer l'utilisateur
            </button>
        </div>
    </form>
</div>
@endsection
