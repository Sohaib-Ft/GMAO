@extends('layouts.app')

@section('title', 'Détails Intervention')


@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    

    <!-- Main Card -->
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        <!-- Status Banner -->
        <div class="px-8 py-4 flex items-center justify-between {{ $workOrder->statut === 'en_cours' ? 'bg-amber-50 border-b border-amber-100' : 'bg-blue-50 border-b border-blue-100' }}">
            <div class="flex items-center gap-3">
                @php
                    $statusClasses = [
                        'nouvelle' => 'bg-blue-100 text-blue-700',
                        'en_attente' => 'bg-indigo-100 text-indigo-700',
                        'en_cours' => 'bg-amber-100 text-amber-700',
                        'terminee' => 'bg-green-100 text-green-700',
                        'annulee' => 'bg-red-100 text-red-700',
                    ];
                @endphp
                <span class="px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider {{ $statusClasses[$workOrder->statut] ?? 'bg-gray-100' }}">
                    {{ str_replace('_', ' ', $workOrder->statut) }}
                </span>
                
                @php
                    $priorityClasses = [
                        'basse' => 'bg-gray-100 text-gray-600',
                        'normale' => 'bg-blue-100 text-blue-600',
                        'haute' => 'bg-orange-100 text-orange-600',
                        'urgente' => 'bg-red-100 text-red-600',
                    ];
                @endphp
                <span class="px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider {{ $priorityClasses[$workOrder->priorite] ?? 'bg-gray-100' }}">
                    Priorité {{ $workOrder->priorite }}
                </span>
            </div>
            <div class="text-xs text-gray-500">
                Créé le {{ $workOrder->created_at->format('d/m/Y à H:i') }}
            </div>
        </div>

        <div class="p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $workOrder->titre }}</h2>
            <div class="bg-gray-50 p-6 rounded-2xl text-gray-600 leading-relaxed italic mb-8 border border-gray-100">
                "{{ $workOrder->description }}"
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Equipment Info -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest flex items-center">
                        <i class='bx bx-box mr-2 text-lg'></i> Équipement concerné
                    </h3>
                    <div class="flex items-center p-4 bg-white border border-gray-100 rounded-2xl shadow-sm">
                        <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mr-4">
                            <i class='bx bx-wrench text-2xl'></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">{{ $workOrder->equipement->nom ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">Code : {{ $workOrder->equipement->code ?? 'N/A' }}</p>
                            <p class="text-xs text-blue-600 font-medium mt-1">
                                <i class='bx bx-map-pin mr-1'></i>{{ $workOrder->equipement->site ?? 'Localisation...' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Demandeur Info -->
                 <div class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest flex items-center">
                        <i class='bx bx-user mr-2 text-lg'></i> Demandeur
                    </h3>
                    <div class="flex items-center p-4 bg-white border border-gray-100 rounded-2xl shadow-sm">
                        <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mr-4">
                            <i class='bx bx-user-circle text-2xl'></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">{{ $workOrder->employe->name ?? 'Système' }}</p>
                            <p class="text-xs text-gray-500">{{ $workOrder->employe->email ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">Poste : Employé</p>
                        </div>
                    </div>
                </div>

                <!-- Technician Info -->
                 <div class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest flex items-center">
                        <i class='bx bx-wrench mr-2 text-lg'></i> Technicien Assigné
                    </h3>
                    <div class="flex items-center p-4 bg-white border border-gray-100 rounded-2xl shadow-sm">
                        <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mr-4">
                            <i class='bx bx-user mr-2 text-2xl'></i>
                        </div>
                        <div>
                            @if($workOrder->technicien)
                                <p class="font-bold text-gray-900">{{ $workOrder->technicien->name }}</p>
                                <p class="text-xs text-gray-500">{{ $workOrder->technicien->email }}</p>
                                <p class="text-xs text-blue-600 font-medium mt-1">
                                    Statut : En service
                                </p>
                            @else
                                <p class="font-bold text-gray-400 italic">Non assigné</p>
                                <p class="text-xs text-gray-400">Aucun technicien sur cette tâche</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Intervention History -->
                
                <!-- Actions -->
                <div class="md:col-span-2 flex flex-wrap gap-4 pt-6 border-t border-gray-100">
                    <a href="{{ route('admin.workorders.edit', $workOrder) }}" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200 flex items-center gap-2">
                        <i class='bx bx-edit text-xl'></i>
                        Modifier l'ordre
                    </a>

                    <a href="{{ route('admin.workorders.index') }}" class="px-6 py-3 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition font-bold flex items-center gap-2 ml-auto">
                        <i class='bx bx-arrow-back'></i> Retour à la liste
                    </a>
                </div>  
            </div>

        </div>
    </div>
</div>
@endsection
