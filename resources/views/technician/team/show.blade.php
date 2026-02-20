@extends('layouts.app')

@section('title', 'Profil Technicien')
@section('header', 'Profil de ' . $user->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-start">
        <a href="{{ route('technician.team.index') }}" class="flex items-center text-gray-500 hover:text-blue-600 transition font-medium">
            <i class='bx bx-arrow-back mr-2'></i> Retour à la liste
        </a>
    </div>

    <!-- Profile Header -->
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        <div class="h-32 bg-gradient-to-r from-gray-900 to-blue-900"></div>
        <div class="px-8 pb-8">
            <div class="relative flex justify-between items-end -mt-12 mb-6">
                <div class="h-24 w-24 bg-white p-2 rounded-3xl shadow-xl">
                    <div class="h-full w-full {{ $user->is_active ? 'bg-gray-900' : 'bg-gray-400' }} text-white rounded-2xl flex items-center justify-center text-4xl font-black">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                </div>
                @if($user->id === Auth::id())
                     <div class="px-8 py-3 bg-gray-100 text-gray-500 rounded-2xl font-bold flex items-center gap-2">
                        <i class='bx bx-user'></i>
                        Moi
                    </div>
                @elseif($user->is_active)
                    <a href="{{ route('technician.messages.chat', $user) }}" class="px-8 py-3 bg-blue-600 text-white rounded-2xl font-bold shadow-xl shadow-blue-100 hover:bg-blue-700 transition flex items-center gap-2">
                        <i class='bx bx-message-square-dots'></i>
                        Lancer une discussion
                    </a>
                @else
                    <button disabled class="px-8 py-3 bg-gray-200 text-gray-400 rounded-2xl font-bold cursor-not-allowed flex items-center gap-2">
                        <i class='bx bx-message-square-x'></i>
                        Discussion indisponible
                    </button>
                @endif
            </div>

            <div class="space-y-1 mt-4">
                <h2 class="text-3xl font-black text-gray-900">{{ $user->name }}</h2>
                <p class="text-gray-500 font-medium flex items-center">
                    <i class='bx bx-award mr-2 text-blue-500'></i>
                    Technicien de Maintenance
                </p>
                <div class="mt-2">
                    @if($user->is_active)
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold uppercase">Actif</span>
                    @else
                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-xs font-bold uppercase">Inactif</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">
                <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Total Réparations</p>
                    <p class="text-2xl font-black text-gray-900">{{ $user->interventions_count }}</p>
                </div>
                <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Statut Actuel</p>
                    <p class="text-xl font-bold {{ $user->is_active ? 'text-emerald-500' : 'text-red-500' }}">
                        {{ $user->is_active ? 'Disponible' : 'Indisponible' }}
                    </p>
                </div>
                <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Membre depuis</p>
                    <p class="text-lg font-bold text-gray-700">{{ $user->created_at->format('M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        <div class="p-6 border-b border-gray-50">
            <h3 class="font-bold text-gray-800">Dernières interventions</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($recentTasks as $task)
            <div class="p-6 hover:bg-gray-50 transition">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-bold text-gray-900">{{ $task->titre }}</p>
                        <p class="text-xs text-gray-400 mt-1">Sur {{ $task->equipement->nom ?? 'N/A' }}</p>
                    </div>
                    <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-lg text-xs font-bold uppercase">
                        {{ $task->statut }}
                    </span>
                </div>
            </div>
            @empty
            <div class="p-10 text-center text-gray-400 italic">
                Aucune activité récente affichée.
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
