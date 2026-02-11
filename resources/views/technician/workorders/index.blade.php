@extends('layouts.app')

@section('title', 'Mes Interventions')
@section('header', 'Mes Ordres de Travail')

@section('content')
<div class="space-y-6">


    <!-- Feedback Messages -->
    @if (session('status'))
        <div role="alert" data-flash class="bg-green-50 border-l-4 border-green-500 p-4 rounded-xl shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class='bx bxs-check-circle text-green-500 text-2xl'></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700 font-bold">{{ session('status') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class='bx bxs-error-circle text-red-500 text-2xl'></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700 font-bold">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif
    

    

    <!-- Search & Filters -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('technician.workorders.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class='bx bx-search text-xl'></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher une tâche, un équipement..." 
                       class="w-full pl-10 pr-4 py-2 bg-gray-50 border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none">
            </div>
            <div class="w-full md:w-48">
                <select name="statut" onchange="this.form.submit()" class="w-full px-4 py-2 bg-gray-50 border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none text-sm font-medium text-gray-600">
                    <option value="">Tous les statuts</option>
                    <option value="nouvelle" {{ request('statut') == 'nouvelle' ? 'selected' : '' }}>Nouvelle</option>
                    <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                </select>
            </div>
            <div class="w-full md:w-48">
                <select name="priorite" onchange="this.form.submit()" class="w-full px-4 py-2 bg-gray-50 border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none text-sm font-medium text-gray-600">
                    <option value="">Priorités</option>
                    <option value="basse" {{ request('priorite') == 'basse' ? 'selected' : '' }}>Basse</option>
                    <option value="normale" {{ request('priorite') == 'normale' ? 'selected' : '' }}>Normale</option>
                    <option value="haute" {{ request('priorite') == 'haute' ? 'selected' : '' }}>Haute</option>
                    <option value="urgente" {{ request('priorite') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                </select>
            </div>
            <div class="w-full md:w-48">
                <select name="assignation" onchange="this.form.submit()" class="w-full px-4 py-2 bg-gray-50 border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none text-sm font-medium text-gray-600">
                    <option value="">Tous les ordres</option>
                    <option value="moi" {{ request('assignation') == 'moi' ? 'selected' : '' }}>Mes tâches </option>
                    <option value="assigne" {{ request('assignation') == 'assigne' ? 'selected' : '' }}>Assignés    </option>
                    <option value="non_assigne" {{ request('assignation') == 'non_assigne' ? 'selected' : '' }}>Non assignés</option>
                </select>
            </div>
            
        </form>
    </div>

    <!-- Table View -->
    <x-work-orders.table :work-orders="$workOrders" role="technician" />

    <!-- MODAL: Marquer comme irréparable -->
    <div id="irreparableModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="closeIrreparableModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class='bx bx-error-circle text-red-600 text-2xl'></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                            <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                Équipement irréparable
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-4">
                                    Vous êtes sur le point de marquer <span id="equipmentNameToFail" class="font-bold text-gray-800">cet équipement</span> comme irréparable ou détruit.
                                </p>
                                <form id="irreparableForm" method="POST" action="">
                                    @csrf
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Raison de l'échec (optionnel)</label>
                                    <textarea name="raison" rows="4" 
                                              class="w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 transition-all outline-none text-sm"
                                              placeholder="Ex: Composant électronique grillé, pièces non disponibles, coût de réparation trop élevé..."></textarea>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                    <button type="submit" form="irreparableForm" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm shadow-red-200 transition-all">
                        Confirmer l'échec de réparation
                    </button>
                    <button type="button" onclick="closeIrreparableModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openIrreparableModal(workOrderId, equipmentName) {
        const form = document.getElementById('irreparableForm');
        const modal = document.getElementById('irreparableModal');
        const nameSpan = document.getElementById('equipmentNameToFail');
        
        if (form && modal && nameSpan) {
            form.action = `/technicien/workorders/${workOrderId}/irreparable`;
            nameSpan.textContent = equipmentName;
            modal.classList.remove('hidden');
        } else {
            console.error('Irreparable modal elements not found');
        }
    }

    function closeIrreparableModal() {
        const modal = document.getElementById('irreparableModal');
        if (modal) modal.classList.add('hidden');
    }
</script>
@endpush
