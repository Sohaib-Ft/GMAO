@extends('layouts.app')

@section('title', 'Liste des Équipements')
@section('header', 'Liste des Équipements')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex flex-1 items-center max-w-4xl w-full">
            <form action="{{ route('employee.equipments.index') }}" method="GET" id="filterForm"
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
                <div class="flex items-center space-x-2 text-gray-500 min-w-[150px] w-full md:w-auto">
                    <i class='bx bx-filter'></i>
                    <select name="statut" id="statutFilter"
                            class="w-full text-sm border-none bg-gray-50 rounded-lg">
                        <option value="">Tous les statuts</option>
                        <option value="actif" {{ request('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ request('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>

                <!-- Localisation -->
                <div class="flex items-center space-x-2 text-gray-500 min-w-[150px] w-full md:w-auto">
                    <i class='bx bx-map-pin'></i>
                    <input type="text" name="localisation" id="locationFilter" value="{{ request('localisation') }}"
                           placeholder="Emplacement..." 
                           class="w-full text-sm border-none bg-gray-50 rounded-lg">
                </div>
            </form>
        </div>
    </div>

    <!-- LIST -->
    <div id="equipments-list-container">
        @include('employee.equipments._list_partial')
    </div>
</div>

<!-- JS -->
<script>
document.addEventListener('DOMContentLoaded', () => {

    const searchInput = document.getElementById('searchInput');
    const statutFilter = document.getElementById('statutFilter');
    const locationFilter = document.getElementById('locationFilter');
    const form = document.getElementById('filterForm');
    const container = document.getElementById('equipments-list-container');

    function updateResults() {
        const params = new URLSearchParams(new FormData(form)).toString();
        const url = `${form.action}?${params}`;

        container.style.opacity = '0.5';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                container.innerHTML = html;
                window.history.pushState({}, '', url);
                container.style.opacity = '1';
            });
    }

    let timer;
    const debounce = (fn, delay) => {
        clearTimeout(timer);
        timer = setTimeout(fn, delay);
    };

    searchInput.addEventListener('input', () => debounce(updateResults, 300));
    locationFilter.addEventListener('input', () => debounce(updateResults, 500));
    statutFilter.addEventListener('change', updateResults);
});
</script>
@endsection
