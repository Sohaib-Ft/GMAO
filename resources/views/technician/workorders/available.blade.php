@extends('layouts.app')

@section('title', 'Ordres Disponibles')
@section('header', 'Ordres de Travail Non Assignés')

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
                    <p class="text-sm text-green-700 font-bold">{{ session('status') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div role="alert" data-flash class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm mb-6">
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
    <div class="bg-white p-4 rounded-3xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" action="{{ route('technician.workorders.available') }}" id="filterForm" class="flex flex-col md:flex-row gap-4">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class='bx bx-search text-xl'></i>
                </span>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Rechercher par titre, code ou équipement..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none text-sm">
            </div>
            <div class="w-full md:w-48">
                <select name="statut" onchange="this.form.submit()" class="w-full px-4 py-2.5 bg-gray-50 border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none text-sm font-medium text-gray-600">
                    <option value="">Tous les statuts</option>
                    <option value="nouvelle" {{ request('statut') == 'nouvelle' ? 'selected' : '' }}>Nouvelle</option>
                    <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                </select>
            </div>
            <div class="w-full md:w-48">
                <select name="priorite" onchange="this.form.submit()" class="w-full px-4 py-2.5 bg-gray-50 border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none text-sm font-medium text-gray-600">
                    <option value="">Priorités</option>
                    <option value="basse" {{ request('priorite') == 'basse' ? 'selected' : '' }}>Basse</option>
                    <option value="normale" {{ request('priorite') == 'normale' ? 'selected' : '' }}>Normale</option>
                    <option value="haute" {{ request('priorite') == 'haute' ? 'selected' : '' }}>Haute</option>
                    <option value="urgente" {{ request('priorite') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Available List -->
    <div id="available-workorders-container" class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        

        <div class="divide-y divide-gray-50">
            @forelse($workOrders as $order)
            <div class="p-8 hover:bg-gray-50/80 transition group">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            @php
                                $priorityClasses = [
                                    'basse' => 'bg-gray-100 text-gray-500',
                                    'normale' => 'bg-blue-100 text-blue-600',
                                    'haute' => 'bg-orange-100 text-orange-600',
                                    'urgente' => 'bg-red-100 text-red-600',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest {{ $priorityClasses[$order->priorite] ?? 'bg-gray-100' }}">
                                Priorité {{ $order->priorite }}
                            </span>
                            <span class="text-xs text-gray-400 font-medium">#{{ $order->id }}</span>
                        </div>
                        
                        <h4 class="text-xl font-bold text-gray-900 mb-2 truncate max-w-2xl">{{ $order->titre }}</h4>
                        <p class="text-gray-500 text-sm line-clamp-2 italic mb-4">"{{ $order->description }}"</p>

                        <div class="flex flex-wrap items-center gap-6 text-sm">
                            <div class="flex items-center text-gray-700 font-semibold">
                                <i class='bx bx-wrench mr-2 text-blue-500'></i>
                                {{ $order->equipement->nom ?? 'N/A' }}
                                <span class="ml-2 px-2 py-0.5 bg-gray-100 text-[10px] text-gray-400 rounded uppercase font-mono">{{ $order->equipement->code ?? '' }}</span>
                            </div>
                            <div class="flex items-center text-gray-500">
                                <i class='bx bx-map-pin mr-2 text-gray-400'></i>
                                {{ $order->equipement->site ?? 'Non spécifié' }}
                            </div>
                            <div class="flex items-center text-gray-400">
                                <i class='bx bx-time mr-2'></i>
                                Publié le {{ $order->created_at->format('d M Y') }}
                            </div>
                        </div>
                    </div>

                    <div class="w-full lg:w-auto">
                        <form action="{{ route('technician.workorders.assign', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full lg:w-auto px-8 py-4 bg-gray-900 text-white rounded-2xl font-bold hover:bg-blue-600 transition transform hover:-translate-y-1 shadow-lg flex items-center justify-center gap-2 group-hover:bg-blue-600">
                                <i class='bx bx-user-plus text-xl'></i>
                                S'assigner la tâche
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="py-24 text-center">
                <div class="h-24 w-24 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6 transform rotate-12">
                    <i class='bx bx-wrench text-5xl text-blue-300'></i>
                </div>
                <h5 class="text-2xl font-black text-gray-900 mb-2">Pas de nouvelles tâches !</h5>
                <p class="text-gray-500 max-w-sm mx-auto">Aucune tâche disponible pour le moment.</p>
            </div>
            @endforelse
        </div>

        @if($workOrders->hasPages())
        <div class="p-8 bg-gray-50 border-t border-gray-100">
            {{ $workOrders->links() }}
        </div>
        @endif
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('filterForm');
    const container = document.getElementById('available-workorders-container');
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
                const newContent = doc.getElementById('available-workorders-container');

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
        
        // Ensure selects trigger the updateResults via form submission or directly
        const selects = form.querySelectorAll('select');
        selects.forEach(select => {
            select.addEventListener('change', () => {
                updateResults();
            });
        });
    }
});
</script>
@endpush
@endsection
