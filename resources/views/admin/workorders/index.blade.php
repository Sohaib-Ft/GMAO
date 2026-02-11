@extends('layouts.app')

@section('title', 'Gestion des Interventions')
@section('header', 'Ordres de travail')

@section('content')
<div class="space-y-6">
    <!-- Header Actions & Welcome -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex flex-col lg:flex-row justify-between items-center gap-6">
            
            <!-- Search and Filters Form -->
            <form action="{{ route('admin.workorders.index') }}" method="GET" id="filterForm" class="flex flex-col md:flex-row items-center gap-4 flex-1 w-full">
                <!-- Search Input -->
                <div class="relative group flex-1 w-full">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-blue-500 transition-colors">
                        <i class='bx bx-search text-xl'></i>
                    </span>
                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                           placeholder="Rechercher par titre, code ou équipement..."
                           class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white shadow-sm transition-all outline-none">
                </div>

                <!-- Status Filter -->
                <select name="statut" onchange="this.form.dispatchEvent(new Event('submit'))" 
                    class="w-full md:w-48 px-4 py-2.5 bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white shadow-sm transition-all outline-none font-medium text-gray-600">
                    <option value="">Tous les statuts</option>
                    <option value="nouvelle" {{ request('statut') == 'nouvelle' ? 'selected' : '' }}>Nouvelle</option>
                    <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                    <option value="terminee" {{ request('statut') == 'terminee' ? 'selected' : '' }}>Terminée</option>
                    <option value="annulee" {{ request('statut') == 'annulee' ? 'selected' : '' }}>Annulée</option>
                </select>

                <!-- Priority Filter -->
                <select name="priorite" onchange="this.form.dispatchEvent(new Event('submit'))"
                    class="w-full md:w-48 px-4 py-2.5 bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white shadow-sm transition-all outline-none font-medium text-gray-600">
                    <option value="">Priorités</option>
                    <option value="basse" {{ request('priorite') == 'basse' ? 'selected' : '' }}>Basse</option>
                    <option value="normale" {{ request('priorite') == 'normale' ? 'selected' : '' }}>Normale</option>
                    <option value="haute" {{ request('priorite') == 'haute' ? 'selected' : '' }}>Haute</option>
                    <option value="urgente" {{ request('priorite') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                </select>
            </form>
            
            <!-- Create Button -->
            <a href="#" 
               class="flex items-center justify-center px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-200 font-bold group whitespace-nowrap w-full lg:w-auto">
                <i class='bx bx-plus-circle mr-2 text-xl group-hover:rotate-90 transition-transform'></i>
                Créer un ordre
            </a>
        </div>
    </div>

    <!-- Table Container -->
    <div id="workorders-table-container">
        <x-work-orders.table :workOrders="$workOrders" role="admin" />
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('filterForm');
    const container = document.getElementById('workorders-table-container');
    const searchInput = document.getElementById('searchInput');

    function updateResults() {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();
        const url = `${form.action}?${params}`;

        // Loading state
        container.style.opacity = '0.5';

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

    // Listen for search input
    if (searchInput) {
        searchInput.addEventListener('input', () => debounce(updateResults, 300));
    }

    // Handle form submission (including select changes)
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
