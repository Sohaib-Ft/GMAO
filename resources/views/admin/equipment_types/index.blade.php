@extends('layouts.app')

@section('title', 'Gestion des Types d\'Équipement')
@section('header', 'Type des équipements')

@section('content')
<div class="space-y-6" x-data="{ 
    showModal: false, 
    isEdit: false, 
    currentId: null, 
    currentName: '',
    openCreate() {
        this.isEdit = false;
        this.currentId = null;
        this.currentName = '';
        this.showModal = true;
    },
    openEdit(id, name) {
        this.isEdit = true;
        this.currentId = id;
        this.currentName = name;
        this.showModal = true;
    }
}">
    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <form action="{{ route('equipment_types.index') }}" method="GET" id="filterForm" class="flex flex-1 items-center max-w-lg w-full">
            <div class="relative w-full">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class='bx bx-search'></i>
                </span>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" 
                    placeholder="Rechercher un type..." 
                    class="pl-10 pr-10 w-full text-sm border-gray-200 bg-gray-50 rounded-xl focus:ring-indigo-500 transition-all outline-none py-2.5">
              
            </div>
        </form>
        <button @click="openCreate()" class="group flex items-center px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-200 font-medium w-full md:w-auto">
            <i class='bx bx-plus-circle mr-2 text-xl group-hover:rotate-90 transition-transform'></i> Nouveau Type
        </button>
    </div>

    

    <!-- Table -->
    <div id="types-table-container" class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Type d'Équipement</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Utilisations</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($types as $type)
                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-2 h-2 rounded-full bg-indigo-400 mr-3"></div>
                                <span class="font-semibold text-gray-700">{{ $type->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold">
                                {{ $type->equipements_count }} équipement(s)
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button @click="openEdit({{ $type->id }}, '{{ addslashes($type->name) }}')" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Modifier">
                                <i class='bx bx-edit-alt text-xl'></i>
                            </button>
                            <button type="button" onclick="openDeleteModal('{{ route('equipment_types.destroy', $type) }}', '{{ addslashes($type->name) }}')"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                    {{ $type->equipements_count > 0 ? 'disabled' : 'title=Supprimer' }}>
                                <i class='bx bx-trash text-xl {{ $type->equipements_count > 0 ? 'opacity-30' : '' }}'></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <td colspan="4" class="py-12 text-center text-gray-500">
                        <i class='bx bx-search-alt text-6xl mb-4 opacity-20'></i>
                                <p class="font-semibold">Aucun type trouvé</p>
                            </td>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL -->
    <div x-show="showModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity bg-gray-900/50 backdrop-blur-sm" 
                 @click="showModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 border border-gray-100">
                
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-500 transition">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>

                <div>
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 mr-4 text-2xl">
                            <i class='bx bx-category'></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900" x-text="isEdit ? 'Modifier le Type' : 'Nouveau Type'"></h3>
                            <p class="text-sm text-gray-500">Définissez une nouvelle catégorie d'équipement.</p>
                        </div>
                    </div>

                    <form :action="isEdit ? '/admin/equipment_types/' + currentId : '{{ route('equipment_types.store') }}'" method="POST">
                        @csrf
                        <template x-if="isEdit">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nom du Type</label>
                            <input type="text" name="name" x-model="currentName" required
                                   class="w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all outline-none"
                                   placeholder="Ex: Climatiseur, Serveur, Machine outils...">
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="submit" class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200" x-text="isEdit ? 'Mettre à jour' : 'Enregistrer'"></button>
                            <button type="button" @click="showModal = false" class="flex-1 px-6 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- DELETE CONFIRMATION MODAL -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="closeDeleteModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class='bx bx-error-circle text-red-600 text-2xl'></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                Supprimer le type
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Êtes-vous sûr de vouloir supprimer <span id="itemNameToDelete" class="font-bold text-gray-800">ce type</span> ?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm shadow-red-200 transition-all">
                            Supprimer définitivement
                        </button>
                    </form>
                    <button type="button" onclick="closeDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
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
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const form = document.getElementById('filterForm');
    const container = document.getElementById('types-table-container');

    function updateResults() {
        const params = new URLSearchParams(new FormData(form)).toString();
        const url = `${form.action}?${params}`;

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newContent = doc.getElementById('types-table-container');

                if (newContent) {
                    container.innerHTML = newContent.innerHTML;
                    window.history.pushState({}, '', url);
                }
            });
    }

    let timer;
    const debounce = (fn, delay) => {
        clearTimeout(timer);
        timer = setTimeout(fn, delay);
    };

    searchInput.addEventListener('input', () => debounce(updateResults, 100));
    
    // Prevent form from reloading page when pressing enter
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        updateResults();
    });

    // Delete modal handlers
    window.openDeleteModal = function(actionUrl, itemName) {
        const form = document.getElementById('deleteForm');
        const modal = document.getElementById('deleteModal');
        const nameSpan = document.getElementById('itemNameToDelete');
        if (form && modal && nameSpan) {
            form.action = actionUrl;
            nameSpan.textContent = itemName;
            modal.classList.remove('hidden');
        } else {
            console.error('Delete modal elements not found');
        }
    }

    window.closeDeleteModal = function() {
        const modal = document.getElementById('deleteModal');
        if (modal) modal.classList.add('hidden');
    }
});
</script>
@endpush
