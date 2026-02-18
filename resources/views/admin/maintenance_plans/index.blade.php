@extends('layouts.app')

@section('title', 'Plans de Maintenance')
@section('header', 'Gestion des équipements à maintenance préventive')

@section('content')
<div class="space-y-6">
    <!-- Feedback Messages -->
    @if(session('status'))
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

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm mb-6" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class='bx bxs-error-circle text-red-500 text-2xl'></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700 font-bold">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex flex-1 items-center max-w-2xl">
            <form action="{{ route('maintenance-plans.index') }}" method="GET" id="filterForm" class="flex flex-1 items-center gap-4">
                <!-- Search Bar -->
                <div class="relative w-full">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class='bx bx-search'></i>
                    </span>
                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}" 
                        placeholder="Rechercher par équipement ou code..." 
                        class="pl-10 pr-10 w-full text-sm border-gray-200 bg-gray-50 rounded-xl focus:ring-blue-500 transition-all outline-none py-2.5">
                </div>

            </form>
        </div>
        <a href="{{ route('maintenance-plans.create') }}" class="group flex items-center px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-200 font-medium">
            <i class='bx bx-plus-circle mr-2 text-xl group-hover:rotate-90 transition-transform'></i>
            Nouveau Plan
        </a>
    </div>

    <!-- Container for updates -->
    <div id="plans-list-container">
        <!-- Plans Table -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-left">
                            <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Équipement</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Technicien</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Prochaine Date</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($plans as $plan)
                            <tr class="hover:bg-gray-50/50 transition group">
                                <td class="px-8 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm border-2 border-white shadow-sm group-hover:scale-110 transition-transform">
                                                <i class='bx bx-wrench'></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900">{{ $plan->equipement->nom }}</div>
                                            <div class="text-sm text-gray-500">{{ $plan->equipement->code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($plan->type === 'preventive')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Préventive
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                            Corrective
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <i class='bx bx-user mr-2 text-gray-400'></i>
                                        {{ $plan->technician->name ?? 'Non assigné' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($plan->prochaine_date)
                                        <div class="flex items-center">
                                            <i class='bx bx-calendar-event mr-2 text-gray-400'></i>
                                            {{ \Carbon\Carbon::parse($plan->prochaine_date)->format('d/m/Y') }}
                                        </div>
                                        @if(\Carbon\Carbon::parse($plan->prochaine_date)->isPast())
                                            <span class="mt-1 inline-flex px-2 py-0.5 text-xs leading-5 font-bold rounded bg-red-100 text-red-800">
                                                En retard
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-gray-400 text-sm">Non définie</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('maintenance-plans.edit', $plan) }}" 
                                           class="text-gray-400 hover:text-blue-600 transition p-2 rounded-full hover:bg-blue-50" 
                                           title="Modifier">
                                            <i class='bx bx-edit-alt text-lg'></i>
                                        </a>

                                        <button type="button" 
                                                onclick="openDeleteModal('{{ route('maintenance-plans.destroy', $plan) }}', '{{ addslashes($plan->equipement->nom) }}')" 
                                                class="text-gray-400 hover:text-red-600 transition p-2 rounded-full hover:bg-red-50" 
                                                title="Supprimer">
                                            <i class='bx bx-trash text-lg'></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <i class='bx bx-search-alt text-6xl mb-4 opacity-20'></i>
                                        <p class="font-semibold">Aucun plan de maintenance trouvé</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($plans->hasPages())
                <div class="px-8 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $plans->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
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
                            Supprimer le plan de maintenance
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Êtes-vous sûr de vouloir supprimer le plan pour <span id="planNameToDelete" class="font-bold text-gray-800">cet équipement</span> ?
                            </p>
                            <ul class="mt-3 text-sm text-gray-500 list-disc list-inside bg-red-50 p-3 rounded-xl border border-red-100 space-y-1">
                                <li class="text-red-700 font-bold">Cette action est irréversible.</li>
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

<script>
    function openDeleteModal(actionUrl, planName) {
        const form = document.getElementById('deleteForm');
        const modal = document.getElementById('deleteModal');
        const nameSpan = document.getElementById('planNameToDelete');
        
        if(form && modal && nameSpan) {
            form.action = actionUrl;
            nameSpan.textContent = planName;
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

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const form = document.getElementById('filterForm');

        function updateResults() {
            form.submit();
        }

        let timer;
        const debounce = (fn, delay) => {
            clearTimeout(timer);
            timer = setTimeout(fn, delay);
        };

        // Search input with debounce
        if(searchInput) {
            searchInput.addEventListener('input', () => debounce(updateResults, 400));
            
            // Focus at the end of text
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.value = val;
            if(val) searchInput.focus();
        }
    });
</script>
@endsection
