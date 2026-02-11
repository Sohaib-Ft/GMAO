<div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b text-left">
                    <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase">Équipement</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Localisation</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right"></th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($equipments as $equipment)
                <tr class="hover:bg-gray-50 transition">

                    <!-- Équipement -->
                    <td class="px-8 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10">
                                @if($equipment->image_path)
                                    <img src="{{ Storage::url($equipment->image_path) }}"
                                         class="h-10 w-10 rounded-lg object-cover">
                                @else
                                    <div class="h-10 w-10 bg-blue-50 rounded-lg flex items-center justify-center">
                                        <i class='bx bx-wrench text-blue-400'></i>
                                    </div>
                                @endif
                            </div>

                            <div class="ml-4">
                                <div class="font-bold text-gray-900">
                                    {{ $equipment->nom }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $equipment->code }}
                                </div>
                            </div>
                        </div>
                    </td>

                    <!-- Localisation -->
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <div class="flex items-center">
                            <i class='bx bx-map-pin mr-2 text-gray-400'></i>
                            {{ $equipment->site ?? 'Non renseignée' }}
                        </div>
                    </td>

                    <!-- Statut -->
                    <td class="px-6 py-4">
                        @php
                            $statusClasses = [
                                'actif' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                'maintenance' => 'bg-amber-50 text-amber-600 border-amber-100',
                                'panne' => 'bg-rose-50 text-rose-600 border-rose-100',
                                'inactif' => 'bg-gray-50 text-gray-600 border-gray-100',
                            ];
                        @endphp

                        <span class="inline-block px-3 py-1 text-[10px] font-bold uppercase rounded-full border
                            {{ $statusClasses[$equipment->statut] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                            {{ ucfirst($equipment->statut) }}
                        </span>
                    </td>

                    <!-- Action -->
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('employee.equipments.show', $equipment) }}"
                           class="inline-flex items-center px-3 py-1 bg-gray-800 text-white text-xs rounded-lg hover:bg-black transition">
                            <i class='bx bx-show-alt mr-1'></i>
                            Détails
                        </a>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class='bx bx-search-alt text-4xl mb-2 text-gray-200'></i>
                            <p class="font-semibold">Aucun équipement trouvé</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($equipments->hasPages())
        <div class="p-4 bg-gray-50 border-t">
            {{ $equipments->appends(request()->query())->links() }}
        </div>
    @endif
</div>
