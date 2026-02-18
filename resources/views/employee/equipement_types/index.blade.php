@extends('layouts.app')

@section('title', 'Gestion des Types d\'Équipement')
@section('header', 'Type des équipements')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <form action="{{ route('employee.equipment_types.index') }}" method="GET" id="filterForm" class="flex flex-1 items-center max-w-lg w-full">
            <div class="relative w-full">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class='bx bx-search'></i>
                </span>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" 
                    placeholder="Rechercher un type..." 
                    class="pl-10 pr-10 w-full text-sm border-gray-200 bg-gray-50 rounded-xl focus:ring-indigo-500 transition-all outline-none py-2.5">
            </div>
        </form>
    </div>

    <!-- Table -->
    <div id="types-table-container" class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Type d'Équipement</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Utilisations</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-right"></th>
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
                        <td class="px-6 py-4 text-right"></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3">
                            <div class="flex flex-col items-center justify-center text-gray-400 py-12">
                                <i class='bx bx-search-alt text-6xl mb-4 opacity-20'></i>
                                <p class="font-semibold">Aucun type trouvé</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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
    
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        updateResults();
    });
});
</script>
@endpush
