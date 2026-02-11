@extends('layouts.app')

@section('title', 'Mon Profil')

@section('header', 'Mon Profil')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-12">
    
    <!-- Success Message -->
    @if (session('status') === 'profile-updated' || session('status') === 'password-updated')
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-xl shadow-sm mb-6 flex items-center" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
            <i class='bx bxs-check-circle mr-3 text-xl'></i>
            <span>{{ session('status') === 'profile-updated' ? 'Profil mis à jour avec succès.' : 'Mot de passe mis à jour avec succès.' }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Summary & Info -->
        <div class="lg:col-span-1 space-y-8">
            <!-- Profile Card -->
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
                <div class="h-32 bg-[#1e293b] relative">
                    <div class="absolute -bottom-12 left-1/2 transform -translate-x-1/2">
                        <div class="relative group">
                            <div class="w-24 h-24 rounded-full border-4 border-white bg-blue-100 flex items-center justify-center overflow-hidden shadow-lg">
                                <i class='bx bxs-user text-5xl text-blue-500'></i>
                            </div>
                            <button class="absolute bottom-0 right-0 bg-blue-600 text-white p-1.5 rounded-full shadow-lg hover:bg-blue-700 transition group-hover:scale-110">
                                <i class='bx bxs-camera'></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="pt-16 pb-8 px-6 text-center">
                    <h3 class="text-xl font-bold text-gray-800">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    
                    <div class="mt-4 inline-flex items-center px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-bold uppercase tracking-wider">
                        <i class='bx bxs-shield-alt mr-1'></i>
                        {{ $user->role }}
                    </div>
                    
                    <div class="mt-8 border-t border-gray-50 pt-6">
                        <div class="flex justify-between text-xs text-gray-500 mb-2">
                            <span>Membre depuis :</span>
                            <span class="font-bold text-gray-700">{{ $user->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role Permissions Note -->
        
        </div>

        <!-- Right Column: Forms -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Personal Information Form -->
            <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="p-3 bg-gray-100 rounded-2xl text-gray-600">
                        <i class='bx bxs-id-card text-2xl'></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 font-sans">Informations Personnelles</h3>
                        <p class="text-sm text-gray-500">Mettez à jour votre nom et votre adresse email.</p>
                    </div>
                </div>

                <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf
                    @method('patch')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nom Complet</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Adresse Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-[#1e293b] text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:bg-gray-800 transition transform hover:-translate-y-0.5">
                            Sauvegarder les modifications
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security / Password Update Form -->
            <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="p-3 bg-red-50 rounded-2xl text-red-600">
                        <i class='bx bxs-lock-alt text-2xl'></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 font-sans">Sécurité du Compte</h3>
                        <p class="text-sm text-gray-500">Changez votre mot de passe pour maintenir la sécurité.</p>
                    </div>
                </div>

                <form method="post" action="{{ route('user.password.update') }}" class="space-y-6">
                    @csrf
                    @method('put')

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Mot de passe actuel</label>
                        <input type="password" name="current_password" required class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition">
                        @error('current_password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nouveau mot de passe</label>
                            <input type="password" name="password" required class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition">
                            @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Confirmer le mot de passe</label>
                            <input type="password" name="password_confirmation" required class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition transform hover:-translate-y-0.5">
                            Mettre à jour le mot de passe
                        </button>
                    </div>
                </form>
                
                <!-- Danger Zone Placeholder -->
                <div class="mt-10 pt-8 border-t border-gray-100">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Sécurité avancée</h4>
                    <button disabled class="flex items-center text-red-400 font-bold opacity-50 cursor-not-allowed text-sm hover:text-red-500 transition">
                        <i class='bx bx-log-out-circle mr-2'></i>
                        Se déconnecter de tous les autres appareils (Bientôt disponible)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
