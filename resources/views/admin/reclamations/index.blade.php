@extends('layouts.app')

@section('title', 'Gestion des Réclamations')
@section('header', 'Réclamations de mot de passe')

@section('content')
<div class="space-y-6">
    <!-- Header Actions & Filters -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
        <form id="filterForm" action="{{ route('reclamations.index') }}" method="GET" class="flex flex-col md:flex-row flex-1 items-center gap-4 w-full">
            <!-- Search bar -->
            <div class="relative flex-1 w-full">
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" 
                    placeholder="Rechercher par nom ou email..." 
                    class="block w-full px-4 py-2.5 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
            </div>

            <!-- Role filter -->
            <div class="w-full md:w-48">
                <select name="role" onchange="this.form.submit()" class="block w-full py-2.5 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm cursor-pointer">
                    <option value="">Tous les rôles</option>
                    <option value="employe" {{ request('role') == 'employe' ? 'selected' : '' }}>Utilisateur Standard</option>
                    <option value="technicien" {{ request('role') == 'technicien' ? 'selected' : '' }}>Technicien</option>
                </select>
            </div>

            <!-- Date filter -->
            <div class="w-full md:w-48">
                <input type="date" name="date" onchange="this.form.submit()" value="{{ request('date') }}"
                    class="block w-full py-2.5 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm cursor-pointer">
            </div>

        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('searchInput');
                const filterForm = document.getElementById('filterForm');
                let timeout = null;

                searchInput.addEventListener('input', function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        filterForm.submit();
                    }, 500); // Attend 500ms après la fin de la saisie
                });

                // Replacer le curseur à la fin du texte après le rechargement
                if (searchInput.value.length > 0) {
                    searchInput.focus();
                    searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
                }
            });
        </script>
    </div>

    <!-- Reclamations Table -->
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        @if(session('status'))
            <div class="m-6 p-4 bg-green-50 text-green-700 rounded-xl flex items-center shadow-sm">
                <i class='bx bx-check-circle mr-2 text-xl'></i>
                {{ session('status') }}
            </div>
        @endif

        @if(session('error'))
            <div class="m-6 p-4 bg-red-50 text-red-700 rounded-xl flex items-center shadow-sm">
                <i class='bx bx-error-circle mr-2 text-xl'></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-left border-b border-gray-100">
                        <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Utilisateur</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Role</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Date de réclamation</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-center">Statut</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($reclamations as $reclamation)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-8 py-5">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold mr-3 shadow-sm">
                                        {{ substr($reclamation->nom, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ $reclamation->nom }}</div>
                                        <div class="text-xs text-gray-400">{{ $reclamation->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                @if($reclamation->user)
                                    <span class="px-2 py-0.5 rounded-lg text-xs font-bold border 
                                        {{ $reclamation->user->role === 'technicien' ? 'bg-purple-50 text-purple-600 border-purple-100' : 'bg-blue-50 text-blue-600 border-blue-100' }}">
                                        {{ $reclamation->user->role === 'technicien' ? 'Technicien' : 'Standard' }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400 italic">Inconnu</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-sm text-gray-600">
                                {{ $reclamation->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-5 text-center">
                                @if($reclamation->status === 'en_attente')
                                    <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-[10px] font-bold border border-amber-200 uppercase tracking-wider">En attente</span>
                                @elseif($reclamation->status === 'traitee')
                                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-bold border border-green-200 uppercase tracking-wider">Traitée</span>
                                @else
                                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-[10px] font-bold border border-red-200 uppercase tracking-wider">Refusée</span>
                                @endif
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center justify-end gap-2">
                                    @if($reclamation->user)
                                        <a href="{{ route('users.edit', $reclamation->user) }}" class="flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-xs font-bold hover:bg-gray-200 transition">
                                            <i class='bx bx-user mr-1'></i> Profil
                                        </a>
                                    @endif

                                    <form action="{{ route('reclamations.process', $reclamation) }}" method="POST" class="inline">
                                        @csrf
                                        @if($reclamation->status === 'traitee')
                                            <button type="submit" class="px-3 py-1.5 bg-gray-600 text-white rounded-lg text-xs font-bold hover:bg-gray-700 transition shadow-md shadow-gray-100">
                                                <i class='bx bx-refresh mr-1'></i> Renvoyer
                                            </button>
                                        @elseif($reclamation->status === 'en_attente')
                                            <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 transition shadow-md shadow-blue-100">
                                                <i class='bx bx-send mr-1'></i> Traiter
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-10 text-center">
                                <div class="flex flex-col items-center text-gray-400">
                                    <i class='bx bx-comment-minus text-5xl mb-2 opacity-50'></i>
                                    <span class="italic">Aucune réclamation trouvée.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reclamations->hasPages())
            <div class="px-8 py-4 bg-gray-50 border-t border-gray-100">
                {{ $reclamations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
