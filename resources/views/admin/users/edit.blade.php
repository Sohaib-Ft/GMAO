@extends('layouts.app')

@section('title', 'Modifier l\'utilisateur')
@section('header', 'Modifier l\'utilisateur')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow border border-gray-100">
    @if(session('status'))
            <div role="alert" data-flash class="mb-4 text-green-600 font-medium flex items-center">
                <i class='bx bx-check-circle mr-2'></i>
                {{ session('status') }}
            </div>
        @endif

    <div class="mb-6 flex items-center justify-between">
        <h3 class="text-xl font-bold text-gray-800">Modifier : {{ $user->name }}</h3>
        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
            {{ $user->role === 'technicien' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
            {{ ucfirst($user->role) }}
        </span>
    </div>

    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="mb-5 text-gray-400 uppercase text-xs font-bold tracking-widest border-b pb-2 mb-4">Informations Générales</div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Nom complet</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class='bx bx-user'></i>
                    </span>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" 
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
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" 
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
                    <option value="technicien" {{ old('role', $user->role) == 'technicien' ? 'selected' : '' }}>Technicien</option>
                    <option value="employe" {{ old('role', $user->role) == 'employe' ? 'selected' : '' }}>Utilisateur standard</option>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            @error('role') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mt-8 mb-5 text-gray-400 uppercase text-xs font-bold tracking-widest border-b pb-2">Sécurité (Laisser vide pour ne pas changer)</div>

        <div class="mb-4">
            <label class="block text-sm font-bold text-gray-700 mb-2">Nouveau mot de passe</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class='bx bx-lock-alt'></i>
                </span>
                <input type="password" name="password" placeholder="Min. 8 caractères"
                    class="pl-10 block w-full rounded-xl border-gray-300 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>
            @error('password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-100 space-x-3">
            <a href="{{ route('users.index') }}" class="px-6 py-2 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">
                Annuler
            </a>
            <a href="{{ route('users.show', $user) }}" class="px-6 py-2 text-sm font-bold text-gray-700 bg-white border rounded-xl hover:bg-gray-50 transition-colors">
                Profil
            </a>
            <button type="submit" class="px-8 py-2.5 bg-[#1e293b] text-white rounded-xl font-bold shadow-lg hover:bg-gray-800 transition transform hover:-translate-y-0.5">
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection
