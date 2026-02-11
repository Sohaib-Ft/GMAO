@extends('layouts.app')

@section('title', 'Détails Intervention')


@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    

    <!-- Main Card -->
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        <!-- Status Banner -->
        <div class="px-8 py-4 flex items-center justify-between {{ $workOrder->statut === 'en_cours' ? 'bg-amber-50 border-b border-amber-100' : 'bg-blue-50 border-b border-blue-100' }}">
            <div class="flex items-center gap-3">
            </div>
            <div class="text-xs text-gray-500">
                Assigné le {{ $workOrder->created_at->format('d/m/Y à H:i') }}
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

                <!-- Contact Info -->
                 <div class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest flex items-center">
                        <i class='bx bx-user mr-2 text-lg'></i> Reclameur
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
                <!-- Actions -->
                <div class="md:col-span-2 flex flex-wrap gap-4 pt-6 border-t border-gray-100">
                    @if($workOrder->technicien_id === null)
                         <form action="{{ route('technician.workorders.assign', $workOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 transition shadow-lg shadow-emerald-200 flex items-center gap-2">
                                <i class='bx bx-user-plus text-xl'></i>
                                S'assigner la tâche
                            </button>
                        </form>
                    @elseif($workOrder->technicien_id === auth()->id())
                        @if($workOrder->statut === 'en_attente')
                            <form action="{{ route('technician.workorders.start', $workOrder) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200 flex items-center gap-2">
                                    <i class='bx bx-play-circle text-xl'></i>
                                    Démarrer l'intervention
                                </button>
                            </form>
                        @endif

                        @if($workOrder->statut === 'en_cours')
                            <form action="{{ route('technician.workorders.complete', $workOrder) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 transition shadow-lg shadow-green-200 flex items-center gap-2">
                                    <i class='bx bx-check-circle text-xl'></i>
                                    Terminer
                                </button>
                            </form>

                            <button type="button" onclick="openIrreparableModal({{ $workOrder->id }}, '{{ addslashes($workOrder->equipement->nom ?? 'cet équipement') }}')" 
                                    class="px-6 py-3 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition shadow-lg shadow-red-200 flex items-center gap-2">
                                <i class='bx bx-x-circle text-xl'></i>
                                Irréparable
                            </button>
                        @endif
                    @endif

                    <a href="{{ route('technician.workorders.index') }}" class="px-6 py-3 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition font-bold flex items-center gap-2 ml-auto">
                        <i class='bx bx-arrow-back'></i> Retour
                    </a>
                </div>  
            </div>

        </div>
    </div>
</div>

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
        }
    }

    function closeIrreparableModal() {
        const modal = document.getElementById('irreparableModal');
        if (modal) modal.classList.add('hidden');
    }
</script>
@endpush
