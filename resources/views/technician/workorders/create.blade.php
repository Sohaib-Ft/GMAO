@extends('layouts.app')

@section('title', 'Nouveau Bon de Travail')
@section('header', 'Créer un Bon de Travail')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        <div class="p-8">
            <h2 class="text-2xl font-black text-gray-900 mb-6 uppercase tracking-tight">Nouvelle Intervention</h2>
            
            <form action="{{ route('technician.workorders.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="maintenance_plan_id" value="{{ $maintenancePlanId }}">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Équipement -->
                    <div class="space-y-2">
                        <label for="equipement_id" class="text-xs font-black text-gray-400 uppercase tracking-widest">Équipement</label>
                        <select name="equipement_id" id="equipement_id" class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none text-sm font-bold text-gray-900">
                            <option value="">Sélectionner l'équipement</option>
                            @foreach($equipments as $equipment)
                                <option value="{{ $equipment->id }}" {{ (isset($selectedEquipmentId) && $selectedEquipmentId == $equipment->id) ? 'selected' : '' }}>
                                    [{{ $equipment->code }}] {{ $equipment->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('equipement_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Priorité -->
                    <div class="space-y-2">
                        <label for="priorite" class="text-xs font-black text-gray-400 uppercase tracking-widest">Priorité</label>
                        <select name="priorite" id="priorite" class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none text-sm font-bold text-gray-900">
                            <option value="basse">Basse</option>
                            <option value="normale" selected>Normale</option>
                            <option value="haute">Haute</option>
                            <option value="urgente">Urgente</option>
                        </select>
                    </div>
                </div>

                <!-- Titre -->
                <div class="space-y-2">
                    <label for="titre" class="text-xs font-black text-gray-400 uppercase tracking-widest">Titre de l'intervention</label>
                    <input type="text" name="titre" id="titre" value="{{ $type === 'preventive' ? 'Maintenance préventive périodique' : old('titre') }}" 
                           placeholder="Ex: Remplacement des filtres, Graissage..."
                           class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none text-sm font-bold text-gray-900">
                    @error('titre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <label for="description" class="text-xs font-black text-gray-400 uppercase tracking-widest">Description du travail à effectuer</label>
                    <textarea name="description" id="description" rows="4" 
                              placeholder="Détails de l'intervention..."
                              class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none text-sm font-bold text-gray-900">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4 flex items-center justify-end gap-4">
                    <a href="{{ url()->previous() }}" class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 transition">Annuler</a>
                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition shadow-lg shadow-blue-100">
                        Créer et Démarrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
