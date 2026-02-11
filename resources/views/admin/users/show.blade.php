@extends('layouts.app')

@section('title', 'Profil utilisateur')
@section('header', 'Profil utilisateur')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Profile Header Card -->
    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-3xl shadow-xl overflow-hidden">
        <div class="p-8">
            <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                <!-- Avatar -->
                <div class="relative">
                    <div class="h-28 w-28 rounded-full bg-white/20 backdrop-blur-sm text-white flex items-center justify-center font-bold text-4xl shadow-2xl ring-4 ring-white/30">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="absolute -bottom-2 -right-2 h-10 w-10 rounded-full bg-green-400 border-4 border-white flex items-center justify-center shadow-lg">
                        <i class='bx bx-check text-white text-xl font-bold'></i>
                    </div>
                </div>

                <!-- User Info -->
                <div class="flex-1 text-center md:text-left">
                    <h2 class="text-3xl font-bold text-white mb-2">{{ $user->name }}</h2>
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-4">
                        <div class="flex items-center text-white/90">
                            <i class='bx bx-envelope mr-2 text-xl'></i>
                            <span class="text-sm">{{ $user->email }}</span>
                        </div>
                    </div>
                    @php
                        $roleData = [
                            'admin' => ['bg' => 'bg-blue-500/30', 'text' => 'text-white', 'border' => 'border-white/40', 'icon' => 'bx-crown'],
                            'technicien' => ['bg' => 'bg-purple-500/30', 'text' => 'text-white', 'border' => 'border-white/40', 'icon' => 'bx-wrench'],
                            'employe' => ['bg' => 'bg-green-500/30', 'text' => 'text-white', 'border' => 'border-white/40', 'icon' => 'bx-user'],
                        ];
                        $roleInfo = $roleData[$user->role] ?? ['bg' => 'bg-gray-500/30', 'text' => 'text-white', 'border' => 'border-white/40', 'icon' => 'bx-user'];
                    @endphp
                    <div class="inline-flex items-center px-4 py-2 {{ $roleInfo['bg'] }} {{ $roleInfo['text'] }} rounded-full text-sm font-bold border-2 {{ $roleInfo['border'] }} backdrop-blur-sm">
                        <i class='bx {{ $roleInfo['icon'] }} mr-2 text-lg'></i>
                        {{ ucfirst($user->role) }}
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <a href="{{ route('users.index') }}" class="px-5 py-2.5 rounded-xl bg-white/20 backdrop-blur-sm text-white border-2 border-white/30 text-sm font-bold hover:bg-white/30 transition-all flex items-center shadow-lg">
                        <i class='bx bx-arrow-back mr-2'></i>
                        Retour
                    </a>
                   
                </div>
            </div>
        </div>
    </div>

    <!-- Information Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Personal Information Card -->
        <div class="bg-white rounded-3xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center mb-6">
                <div class="h-12 w-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center mr-4">
                    <i class='bx bx-user-circle text-2xl'></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Informations personnelles</h3>
            </div>
            <div class="space-y-4">
                <div class="flex items-start p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    <i class='bx bx-user text-gray-400 text-xl mr-3 mt-0.5'></i>
                    <div class="flex-1">
                        <div class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Nom complet</div>
                        <div class="text-sm text-gray-900 font-medium">{{ $user->name }}</div>
                    </div>
                </div>
                <div class="flex items-start p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    <i class='bx bx-envelope text-gray-400 text-xl mr-3 mt-0.5'></i>
                    <div class="flex-1">
                        <div class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Adresse email</div>
                        <div class="text-sm text-gray-900 font-medium">{{ $user->email }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Details Card -->
        <div class="bg-white rounded-3xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center mb-6">
                <div class="h-12 w-12 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center mr-4">
                    <i class='bx bx-shield-quarter text-2xl'></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Détails du compte</h3>
            </div>
            <div class="space-y-4">
                <div class="flex items-start p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    <i class='bx bx-briefcase text-gray-400 text-xl mr-3 mt-0.5'></i>
                    <div class="flex-1">
                        <div class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Rôle</div>
                        <div class="text-sm text-gray-900 font-medium">{{ ucfirst($user->role) }}</div>
                    </div>
                </div>
                <div class="flex items-start p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    <i class='bx bx-calendar text-gray-400 text-xl mr-3 mt-0.5'></i>
                    <div class="flex-1">
                        <div class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Date de création</div>
                        <div class="text-sm text-gray-900 font-medium">{{ $user->created_at->format('d/m/Y à H:i') }}</div>
                    </div>
                </div>
                <div class="flex items-start p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    <i class='bx bx-time text-gray-400 text-xl mr-3 mt-0.5'></i>
                    <div class="flex-1">
                        <div class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Dernière modification</div>
                        <div class="text-sm text-gray-900 font-medium">{{ $user->updated_at->format('d/m/Y à H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   
   
</div>
@endsection
