@extends('layouts.app')

@section('title', 'Mes Demandes d\'Intervention')
@section('header', 'Gestion des ordres de travail')

@section('content')
<div class="space-y-6">
    
    <!-- Feedback Messages -->
    @if (session('status'))
        <div role="alert" data-flash class="bg-green-50 border-l-4 border-green-500 p-4 rounded-xl shadow-sm mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class='bx bxs-check-circle text-green-500 text-2xl'></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700 font-bold">
                        {{ session('status') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Header Actions & Welcome -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
            
            <!-- Filters Form -->
            <form action="{{ route('employee.workorders.index') }}" method="GET" id="filterForm" class="flex flex-col md:flex-row items-center gap-4 flex-1 w-full">
                <!-- Search Input -->
                <div class="relative group flex-1 w-full max-w-md">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-blue-500 transition-colors">
                        <i class='bx bx-search text-xl'></i>
                    </span>
                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                           placeholder="Rechercher une demande..."
                           class="w-full pl-12 pr-4 py-3 bg-gray-50 border-gray-100 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white shadow-sm transition-all outline-none">
                </div>

                <!-- Status Filter -->
                <div class="w-full md:w-48">
                    <select name="statut" onchange="this.form.submit()" 
                        class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white shadow-sm transition-all outline-none font-medium text-gray-600">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="terminee" {{ request('statut') == 'terminee' ? 'selected' : '' }}>Terminée</option>
                    </select>
                </div>

                <!-- Priority Filter -->
                <div class="w-full md:w-48">
                    <select name="priorite" onchange="this.form.submit()"
                        class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white shadow-sm transition-all outline-none font-medium text-gray-600">
                        <option value="">Priorités</option>
                        <option value="basse" {{ request('priorite') == 'basse' ? 'selected' : '' }}>Basse</option>
                        <option value="normale" {{ request('priorite') == 'normale' ? 'selected' : '' }}>Normale</option>
                        <option value="haute" {{ request('priorite') == 'haute' ? 'selected' : '' }}>Haute</option>
                        <option value="urgente" {{ request('priorite') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                    </select>
                </div>
            </form>
            
            <!-- Create Button -->
                <a href="{{ route('employee.workorders.create') }}" 
                   class="group flex items-center justify-center px-8 py-3 bg-blue-600 text-white rounded-2xl hover:bg-blue-700 transition shadow-lg shadow-blue-200 font-bold whitespace-nowrap">
                    <i class='bx bx-plus-circle mr-2 text-xl group-hover:rotate-90 transition-transform'></i>
                    Signaler une panne
                </a>
            
        </div>
    </div>

    <!-- Table Container -->
    <div id="workorders-table-container">
        <x-work-orders.table :workOrders="$workOrders" role="employee" />
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
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
                            Supprimer la demande
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Êtes-vous sûr de vouloir supprimer la demande <span id="itemToDelete" class="font-bold text-gray-800"></span> ?
                            </p>
                            <ul class="mt-3 text-sm text-gray-500 list-disc list-inside bg-red-50 p-3 rounded-xl border border-red-100 space-y-1">
                                <li class="text-red-700">Cette action est irréversible.</li>
                            </ul>
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

@push('scripts')
<script>
    function openDeleteModal(actionUrl, itemName) {
        const form = document.getElementById('deleteForm');
        const modal = document.getElementById('deleteModal');
        const itemSpan = document.getElementById('itemToDelete');
        
        if(form && modal && itemSpan) {
             form.action = actionUrl;
             itemSpan.textContent = itemName;
             modal.classList.remove('hidden');
        } else {
            console.error('Modal elements not found');
        }
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        if(modal) {
            modal.classList.add('hidden');
        }
    }

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('filterForm');
    const container = document.getElementById('workorders-table-container');
    const searchInput = document.getElementById('searchInput');

    function updateResults() {
        const params = new URLSearchParams(new FormData(form)).toString();
        const url = `${form.action}?${params}`;

        // Suble loading effect
        container.style.opacity = '0.6';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newContent = doc.getElementById('workorders-table-container');

                if (newContent) {
                    container.innerHTML = newContent.innerHTML;
                    container.style.opacity = '1';
                    window.history.pushState({}, '', url);
                }
            })
            .catch(err => {
                console.error('Fetch error:', err);
                container.style.opacity = '1';
            });
    }

    let timer;
    const debounce = (fn, delay) => {
        clearTimeout(timer);
        timer = setTimeout(fn, delay);
    };

    if (searchInput) {
        searchInput.addEventListener('input', () => debounce(updateResults, 300));
    }
    
    if (form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            updateResults();
        });
    }
});
</script>
@endpush
@endsection
