@extends('layouts.app')

@section('title', 'Utilisateurs')
@section('header', 'Liste des Utilisateurs')

@section('content')
<div class="space-y-6">
    <!-- Recherche et filtres -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <form action="{{ route('technician.users.index') }}" method="GET" id="filterForm"
                  class="flex flex-col md:flex-row flex-1 items-center gap-4 w-full">

                <!-- Search -->
                <div class="relative flex-1 w-full">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class='bx bx-search text-xl'></i>
                    </span>
                    <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                           placeholder="Rechercher par nom ou email..."
                           class="w-full pl-10 pr-4 py-2 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Statut -->
                <div class="flex items-center space-x-2 text-gray-500 min-w-[150px] w-full md:w-auto">
                    <i class='bx bx-filter'></i>
                    <select id="statutFilter" name="statut" class="w-full text-sm border-none bg-gray-50 rounded-lg">
                        <option value="">Tous les statuts</option>
                        <option value="actif" {{ request('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ request('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>

                <!-- Role -->
                <div class="flex items-center space-x-2 text-gray-500 min-w-[180px] w-full md:w-auto">
                    <i class='bx bx-briefcase'></i>
                    <select id="roleFilter" name="role" class="w-full text-sm border-none bg-gray-50 rounded-lg">
                        <option value="">Tous les rôles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="technicien" {{ request('role') == 'technicien' ? 'selected' : '' }}>Technicien</option>
                        <option value="employe" {{ request('role') == 'employe' ? 'selected' : '' }}>Employé</option>
                    </select>
                </div>

        </form>
    </div>

    <!-- Liste des utilisateurs -->
    <div id="users-list-container" class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Utilisateur</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Rôle</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Statut</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Profil</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div>
                                <div class="font-bold text-gray-800">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold
                                @if($user->role === 'admin') bg-purple-100 text-purple-700
                                @elseif($user->role === 'technicien') bg-blue-100 text-blue-700
                                @else bg-green-100 text-green-700
                                @endif">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold
                                {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('technician.users.show', $user) }}" 
                                class="text-gray-400 hover:text-blue-600 transition p-2 rounded-full hover:bg-blue-50">
                                <i class='bx bx-user text-lg'></i> 
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class='bx bx-search-alt text-4xl mb-2'></i>
                            <p>Aucun utilisateur trouvé</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    const statutFilter = document.getElementById('statutFilter');
    const container = document.getElementById('users-list-container');
    const form = document.getElementById('filterForm');

    function updateResults() {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();
        const url = `${form.action}?${params}`;

        container.style.opacity = '0.5';
        container.style.pointerEvents = 'none';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('users-list-container');
                if (newContent) {
                    container.innerHTML = newContent.innerHTML;
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

    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(updateResults, 300);
        });
    }

    if (roleFilter) roleFilter.addEventListener('change', updateResults);
    if (statutFilter) statutFilter.addEventListener('change', updateResults);
});
</script>
@endpush

@endsection

<style>
.voir {
    font-size: 1.25rem;
    transition: transform 0.2s ease;
}
</style>