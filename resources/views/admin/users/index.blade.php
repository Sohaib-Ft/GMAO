@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs')
@section('header', 'Gestion des utilisateurs')

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

    @if (session('error'))
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
            <form action="{{ route('users.index') }}" method="GET" id="filterForm" class="flex flex-1 items-center gap-4">
                <!-- Search Bar -->
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class='bx bx-search text-xl'></i>
                    </span>
                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}" 
                        placeholder="Rechercher par nom..." 
                        class="w-full pl-10 pr-4 py-2 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        autocomplete="off">
                </div>

                <!-- Role Filter -->
                <div class="flex items-center space-x-2 text-gray-500 min-w-[200px]">
                    <i class='bx bx-filter'></i>
                    <select name="role" id="roleFilter" 
                        class="w-full text-sm border-none bg-gray-50 rounded-lg focus:ring-0 cursor-pointer hover:bg-gray-100 transition">
                        <option value="">Tous les rôles</option>
                        <option value="technicien" {{ request('role') == 'technicien' ? 'selected' : '' }}>Technicien</option>
                        <option value="employe" {{ request('role') == 'employe' ? 'selected' : '' }}>Utilisateur standard</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrateur</option>
                    </select>
                </div>
            </form>
        </div>
        <a href="{{ route('users.create') }}" class="group flex items-center px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-200 font-medium">
            <i class='bx bx-plus-circle mr-2 text-xl group-hover:rotate-90 transition-transform'></i>
            Nouvel Utilisateur
        </a>
    </div>

    <!-- Container for AJAX updates -->
    <div id="users-list-container">
        <!-- Users Table -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-left">
                        <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Rôle</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Date d'ajout</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 transition group">
                            <td class="px-8 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm border-2 border-white shadow-sm group-hover:scale-110 transition-transform">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}

                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $roleLabels = [
                                        'admin' => 'Admin',
                                        'technicien' => 'Technicien',
                                        'employe' => 'Standard'
                                    ];
                                    $roleClasses = [
                                        'admin' => 'bg-blue-100 text-blue-800',
                                        'technicien' => 'bg-purple-100 text-purple-800',
                                        'employe' => 'bg-green-100 text-green-800'
                                    ];
                                    $label = $roleLabels[$user->role] ?? ucfirst($user->role);
                                    $class = $roleClasses[$user->role] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $class }}">
                                    {{ $label }}
                                </span>
                                @if(!$user->is_active)
                                    <span class="ml-2 px-2 py-0.5 inline-flex text-xs leading-5 font-bold rounded bg-red-100 text-red-800">
                                        Désactivé
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex items-center">
                                    <i class='bx bx-calendar mr-2 text-gray-400'></i>
                                    {{ $user->created_at->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                
                                <div class="flex items-center justify-end space-x-2">
                                                                        <a href="{{ route('users.show', $user) }}" class="text-gray-400 hover:text-blue-600 transition p-2 rounded-full hover:bg-blue-50" title="Profil">
                                        <i class='bx bx-user text-lg'></i>
                                    </a>
                                    <!-- Toggle Status Button -->
                                    <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                            class="p-2 rounded-full transition {{ $user->is_active ? 'text-green-500 hover:bg-green-50' : 'text-red-500 hover:bg-red-50' }}" 
                                            title="{{ $user->is_active ? 'Désactiver le compte' : 'Réactiver le compte' }}">
                                            <i class='bx {{ $user->is_active ? 'bx-toggle-right text-2xl' : 'bx-toggle-left text-2xl' }}'></i>
                                        </button>
                                    </form>

                                    

                                    <a href="{{ route('users.edit', $user) }}" class="text-gray-400 hover:text-blue-600 transition p-2 rounded-full hover:bg-blue-50" title="Modifier">
                                        <i class='bx bx-edit-alt text-lg'></i>
                                    </a>



                                    <button type="button" onclick="openDeleteModal('{{ route('users.destroy', $user) }}', '{{ addslashes($user->name) }}')" class="text-gray-400 hover:text-red-600 transition p-2 rounded-full hover:bg-red-50" title="Supprimer">
                                        <i class='bx bx-trash text-lg'></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                               
                                    <i class='bx bx-search-alt text-6xl mb-4 opacity-20'></i>
                                    <p class="font-semibold">Aucun utilisateur trouvé</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-8 py-4 border-t border-gray-100 bg-gray-50">
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<script>
    function openDeleteModal(actionUrl, userName) {
        const form = document.getElementById('deleteForm');
        const modal = document.getElementById('deleteModal');
        const nameSpan = document.getElementById('userNameToDelete');
        
        if(form && modal && nameSpan) {
             form.action = actionUrl;
             nameSpan.textContent = userName;
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
</script>

<!-- Modele de confirmation de suppression -->
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
                            Supprimer l'utilisateur
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Êtes-vous sûr de vouloir supprimer <span id="userNameToDelete" class="font-bold text-gray-800">ce compte</span> ?
                            </p>
                            <ul class="mt-3 text-sm text-gray-500 list-disc list-inside bg-red-50 p-3 rounded-xl border border-red-100 space-y-1">
                                <li class="text-red-700">Toutes les discussions seront définitivement effacées.</li>
                                <li class="text-gray-600">L'historique des interventions sera conservé.</li>
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

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const roleFilter = document.getElementById('roleFilter');
        const container = document.getElementById('users-list-container');
        const form = document.getElementById('filterForm');

        function updateResults() {
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();
            const url = `${form.action}?${params}`;

            // Add a subtle loading state to the table
            container.style.opacity = '0.5';
            container.style.pointerEvents = 'none';

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('users-list-container');
                
                if (newContent) {
                    container.innerHTML = newContent.innerHTML;
                    // Update URL in browser without reload
                    window.history.pushState({}, '', url);
                }
                
                container.style.opacity = '1';
                container.style.pointerEvents = 'auto';
            })
            .catch(error => {
                console.error('Erreur lors de la recherche:', error);
                container.style.opacity = '1';
                container.style.pointerEvents = 'auto';
            });
        }

        // Debounce search input
        let debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(updateResults, 300);
        });

        // Instant role filter
        roleFilter.addEventListener('change', updateResults);
    });
</script>
@endsection
