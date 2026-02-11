@extends('layouts.app')

@section('title', 'Localisations')
@section('header', 'Localisation des équipements')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <form id="filterForm" action="{{ route('technician.localisations.index') }}" class="flex items-center gap-4">
            <div class="relative w-full">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class='bx bx-search-alt text-xl'></i>
                </span>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Rechercher un local..." class="pl-12 pr-4 w-full rounded-xl border-gray-200 bg-gray-50">
            </div>
        </form>
    </div>

    <div id="localisations-table" class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-left">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Nom du site</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Équipements liés</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @forelse($localisations as $loc)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $loc->name }}</td>
                        <td class="px-6 py-4"><span class="px-3 py-1 bg-gray-100 rounded text-sm">{{ $loc->equipements_count }} équipement(s)</span></td>
                        
                    </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center text-gray-400 py-12">
                                    <i class='bx bx-search-alt text-6xl mb-4 opacity-20'></i>
                                    <p class="font-semibold">Aucune localisation trouvée.</p>
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
document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('filterForm');
    const input = document.getElementById('searchInput');
    const container = document.getElementById('localisations-table');

    function update() {
        const params = new URLSearchParams(new FormData(form)).toString();
        fetch(form.action + '?' + params, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newContent = doc.getElementById('localisations-table');
                if (newContent) container.innerHTML = newContent.innerHTML;
            });
    }

    let t; input.addEventListener('input', () => { clearTimeout(t); t = setTimeout(update, 250); });
});
</script>
@endpush
