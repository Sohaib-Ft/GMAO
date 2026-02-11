@extends('layouts.app')

@section('title', 'Gestion des Équipements')
@section('header', 'Gestion des Équipements')

@section('content')
<div class="space-y-6">

    @if(session('status'))
            <div role="alert" data-flash class="mb-4 text-green-600 font-medium flex items-center p-4 bg-green-50 rounded-xl">
                <i class='bx bx-check-circle mr-2 text-xl'></i>
                {{ session('status') }}
            </div>
        @endif

    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex flex-1 items-center max-w-4xl w-full">
            <form action="{{ route('equipments.index') }}" method="GET" id="filterForm"
                  class="flex flex-col md:flex-row flex-1 items-center gap-4 w-full">

                <!-- Search -->
                <div class="relative flex-1 w-full">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class='bx bx-search text-xl'></i>
                    </span>
                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                           placeholder="Rechercher par nom, code..."
                           class="w-full pl-10 pr-4 py-2 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Statut -->
                <div class="flex items-center space-x-2 text-gray-500 min-w-[180px] w-full md:w-auto">
                    <i class='bx bx-filter'></i>
                    <select name="statut" id="statutFilter"
                            class="w-full text-sm border-none bg-gray-50 rounded-lg">
                        <option value="">Tous les statuts</option>
                        <option value="actif" {{ request('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ request('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>

                <!-- Site -->
                <div class="flex items-center space-x-2 text-gray-500 min-w-[150px] w-full md:w-auto">
                    <i class='bx bx-buildings'></i>
                    <select name="site" id="siteFilter" class="w-full text-sm border-none bg-gray-50 rounded-lg">
                        <option value="">Tous les sites</option>
                        @if(!empty($sites) && $sites->count())
                            @foreach($sites as $s)
                                <option value="{{ $s->name }}" {{ request('site') == $s->name ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        @else
                            <!-- Fallback options if no sites in DB -->
                            <option value="Usine A" {{ request('site') == 'Usine A' ? 'selected' : '' }}>Usine A</option>
                            <option value="Usine B" {{ request('site') == 'Usine B' ? 'selected' : '' }}>Usine B</option>
                            <option value="Bureau Principal" {{ request('site') == 'Bureau Principal' ? 'selected' : '' }}>Bureau Principal</option>
                        @endif
                    </select>
                </div>
            </form>
        </div>

        <!-- ADD BUTTON -->
<a href="{{ route('equipments.create') }}"
    id="emptyAddEquipmentBtn"
    class="group flex items-center px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-200 font-medium">
    <i class='bx bx-plus-circle mr-2 text-xl group-hover:rotate-90 transition-transform'></i>
    Ajouter un équipement
</a>


    </div>

    <!-- LIST -->
    <div id="equipments-list-container">
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b text-left">
                            <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase">Équipement</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Info</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse($equipments as $equipment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-8 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10">
                                       @if($equipment->image_path)
    <img src="{{ Storage::url($equipment->image_path) }}" class="h-10 w-10 rounded-lg object-cover">
@else
    <div class="h-10 w-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-400">
        <i class='bx bxs-box text-lg'></i> <!-- Icône équipement -->
    </div>
@endif

                                    </div>
                                    <div class="ml-4">
                                        <div class="font-bold">{{ $equipment->nom }}</div>
                                        <div class="text-xs text-gray-500">{{ $equipment->code }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-sm text-dark-500">
                                
                                <div><strong class='text-gray-700'>Modèle:</strong> {{ $equipment->modele ?? 'N/A' }}</div>
                                <div><strong class='text-gray-700'>Type:</strong> {{ $equipment->type_equipement ?? 'N/A' }}</div>
                           
                                @if($equipment->date_installation)
                                    <div><strong class='text-dark-700'>Installé le:</strong> {{ \Carbon\Carbon::parse($equipment->date_installation)->format('d/m/Y') }}</div>
                                @endif
                                @if($equipment->date_fin_garantie)
                                    <div><strong class='text-dark-700'>Fin garantie:</strong> {{ \Carbon\Carbon::parse($equipment->date_fin_garantie)->format('d/m/Y') }}</div>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                    @if($equipment->statut === 'actif') bg-green-100 text-green-700
                                    @elseif($equipment->statut === 'panne') bg-red-100 text-red-700
                                    @elseif($equipment->statut === 'maintenance') bg-orange-100 text-orange-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ ucfirst($equipment->statut) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
    <!-- Voir -->
    <a href="{{ route('equipments.show', $equipment) }}" class="text-indigo-500 p-2 rounded hover:bg-indigo-50 transition" title="Voir">
        <i class='bx bx-show text-lg'></i>
    </a>

    <!-- Éditer -->
    <a href="{{ route('equipments.edit', $equipment) }}" class="text-blue-500 p-2 rounded hover:bg-blue-50 transition" title="Éditer">
        <i class='bx bx-edit text-lg'></i>
    </a>

    <!-- Supprimer -->
    <button type="button" onclick="openEquipmentDeleteModal('{{ route('equipments.destroy', $equipment) }}', '{{ addslashes($equipment->nom) }}')" class="p-2 text-red-500 hover:bg-red-50 rounded transition" title="Supprimer">
        <i class='bx bx-trash text-lg'></i>
    </button>
</td>

                        </tr>

                        @empty
                        <tr id="no-results-row">
                            <td colspan="4" class="py-12 text-center text-gray-500">
                                <i class='bx bx-search-alt text-6xl mb-4 opacity-20'></i>
                                <p class="font-semibold">Aucun équipement trouvé</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($equipments->hasPages())
            <div class="p-4 bg-gray-50 border-t">
                {{ $equipments->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- JS -->
<script>
document.addEventListener('DOMContentLoaded', () => {

    const searchInput = document.getElementById('searchInput');
    const statutFilter = document.getElementById('statutFilter');
    const siteFilter = document.getElementById('siteFilter');
    const addBtn = document.getElementById('addEquipmentBtn');
    const form = document.getElementById('filterForm');
    const container = document.getElementById('equipments-list-container');

    function isSearching() {
         return searchInput.value.trim() !== '' ||
             (siteFilter && siteFilter.value !== '') ||
             statutFilter.value !== '';
    }

function toggleUI() {
    const topAddBtn = document.getElementById('addEquipmentBtn');
    const emptyAddBtn = document.getElementById('emptyAddEquipmentBtn');

    if (isSearching()) {
        if (topAddBtn) topAddBtn.style.display = 'none';
        if (emptyAddBtn) emptyAddBtn.style.display = 'none';
    } else {
        if (topAddBtn) topAddBtn.style.display = 'flex';
        if (emptyAddBtn) emptyAddBtn.style.display = 'inline-flex';
    }
}


    function updateResults() {
        toggleUI();

        const params = new URLSearchParams(new FormData(form)).toString();
        const url = `${form.action}?${params}`;

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const newContent = doc.getElementById('equipments-list-container');

            if (newContent) {
                container.innerHTML = newContent.innerHTML;
                window.history.pushState({}, '', url);
            }

            toggleUI(); 
});

    }

    let timer;
    const debounce = (fn, delay) => {
        clearTimeout(timer);
        timer = setTimeout(fn, delay);
    };

    searchInput.addEventListener('input', () => debounce(updateResults, 100));
    if (siteFilter) siteFilter.addEventListener('change', updateResults);
    statutFilter.addEventListener('change', updateResults);
});
</script>
<script>
    function openEquipmentDeleteModal(actionUrl, equipmentName) {
        const form = document.getElementById('deleteEquipmentForm');
        const modal = document.getElementById('deleteEquipmentModal');
        const nameSpan = document.getElementById('equipmentNameToDelete');

        if (form && modal && nameSpan) {
            form.action = actionUrl;
            nameSpan.textContent = equipmentName;
            modal.classList.remove('hidden');
        } else {
            console.error('Equipment delete modal elements not found');
        }
    }

    function closeEquipmentDeleteModal() {
        const modal = document.getElementById('deleteEquipmentModal');
        if (modal) modal.classList.add('hidden');
    }
</script>

<!-- Modal confirmation suppression équipement -->
<div id="deleteEquipmentModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="closeEquipmentDeleteModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class='bx bx-error-circle text-red-600 text-2xl'></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                            Supprimer l'équipement
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Êtes-vous sûr de vouloir supprimer <span id="equipmentNameToDelete" class="font-bold text-gray-800">cet équipement</span> ?
                            </p>
                            <ul class="mt-3 text-sm text-gray-500 list-disc list-inside bg-red-50 p-3 rounded-xl border border-red-100 space-y-1">
                                <li class="text-red-700 font-bold">Cette action est irréversible.</li>
                                <li class="text-gray-600">Les documents et images associés seront supprimés.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                <form id="deleteEquipmentForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm shadow-red-200 transition-all">
                        Supprimer définitivement
                    </button>
                </form>
                <button type="button" onclick="closeEquipmentDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
