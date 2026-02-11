@extends('layouts.app')

@section('title', 'Signaler une Panne')
@section('header', 'Nouvelle Demande d\'Intervention')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
        <div class="mb-8 text-center">
            <div class="h-16 w-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class='bx bx-edit text-3xl text-blue-600'></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800">Assignation d'une panne</h3>
            <p class="text-gray-500 text-sm mt-1">
                Remplissez ce formulaire pour assigner un équipement à la maintenance.
            </p>
        </div>

        <form action="{{ route('employee.workorders.store') }}" method="POST" class="space-y-6">
            @csrf
            
                        <!-- Titre (Style matching user image) -->
            <div class="group">
                <label for="titre" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class='bx bx-edit-alt mr-1 text-blue-500'></i> Titre de la demande (Work Order Title)
                </label>
                <input type="text" name="titre" id="titre" value="{{ old('titre') }}" required
                    placeholder="Qu'est-ce qui doit être fait ? (Requis)"
                    class="w-full px-0 py-4 bg-transparent border-0 border-b-2 border-gray-100 focus:border-blue-500 focus:ring-0 transition-all text-xl font-medium placeholder-gray-300">
                @error('titre')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <!-- Équipement avec Recherche (Autocomplete) -->
            <div x-data="{
                open: false,
                search: '',
                selectedId: '{{ old('equipement_id') }}',
                selectedName: '',
                equipments: @js($equipments->map(fn($e) => [
                    'id' => $e->id,
                    'code' => $e->code,
                    'nom' => $e->nom,
                    'type' => $e->equipmentType?->name ?? 'Équipement',
                    'localisation' => $e->localisationRelation?->name ?? 'N/A'
                ])),
                get filteredEquipments() {
                    if (this.search === '') return this.equipments;
                    const q = this.search.toLowerCase();
                    return this.equipments.filter(e => 
                        e.nom.toLowerCase().includes(q) || 
                        e.code.toLowerCase().includes(q)
                    );
                },
                select(e) {
                    this.selectedId = e.id;
                    this.selectedName = `${e.nom} (${e.code})`;
                    this.search = this.selectedName;
                    this.open = false;
                },
                // Initialise le nom si un ID est déjà présent (ex: retour de validation)
                init() {
                    if (this.selectedId) {
                        const found = this.equipments.find(e => e.id == this.selectedId);
                        if (found) this.selectedName = `${found.nom} (${found.code})`;
                        this.search = this.selectedName;
                    }
                }
            }" x-init="init()" class="relative">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class='bx bx-wrench mr-1 text-blue-500'></i> Équipement  <span class="text-red-500">*</span>
                </label>
                
                <input type="hidden" name="equipement_id" :value="selectedId">
                
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-blue-500 transition-colors">
                        <i class='bx bx-search text-xl'></i>
                    </span>
                    <input type="text" 
                           x-model="search"
                           @focus="open = true; if(selectedId) search = ''"
                           @click.away="open = false; if(selectedId && search === '') search = selectedName"
                           placeholder="Commencez à taper le nom ou le code d'inventaire..."
                           class="w-full pl-11 pr-10 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white shadow-sm transition-all outline-none font-medium text-gray-700"
                           autocomplete="off"
                    >
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 pointer-events-none">
                        <i class='bx bx-chevron-down text-xl transition-transform duration-300' :class="open ? 'rotate-180 text-blue-500' : ''"></i>
                    </div>
                </div>

                <!-- Liste des résultats -->
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-2xl max-h-72 overflow-y-auto overflow-x-hidden py-2 border-t-4 border-t-blue-500">
                    
                    <template x-for="eq in filteredEquipments" :key="eq.id">
                        <button type="button" 
                                @click="select(eq)"
                                class="w-full text-left px-5 py-3 hover:bg-blue-50 transition flex items-center gap-4 border-b border-gray-50 last:border-0 group">
                            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center shadow-sm group-hover:bg-blue-600 group-hover:text-white transition-all">
                                <i class='bx bx-hive text-xl'></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors" x-text="eq.nom"></span>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded font-mono uppercase tracking-tighter" x-text="eq.code"></span>
                                    <span class="text-[10px] text-gray-400 italic" x-text="eq.localisation"></span>
                                </div>
                            </div>
                        </button>
                    </template>

                    <div x-show="filteredEquipments.length === 0" class="px-6 py-10 text-center">
                        <div class="text-gray-300 mb-2">
                            <i class='bx bx-search-alt text-4xl'></i>
                        </div>
                        <p class="text-gray-500 text-sm font-medium italic">Aucun équipement trouvé pour "<span x-text="search"></span>"</p>
                    </div>
                </div>
                
                @error('equipement_id')
                    <p class="text-red-500 text-xs mt-1 px-1">{{ $message }}</p>
                @enderror
            </div>



            <!-- Urgence -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class='bx bx-alarm-exclamation mr-1'></i> Niveau d'urgence
                </label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach(['basse' => 'Basse', 'normale' => 'Normale', 'haute' => 'Haute', 'urgente' => 'Urgente'] as $val => $label)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="priorite" value="{{ $val }}" class="peer sr-only" {{ old('priorite', 'normale') == $val ? 'checked' : '' }}>
                        <div class="p-3 text-center bg-gray-50 border border-gray-200 rounded-xl peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 transition-all font-medium text-sm hover:bg-gray-100">
                            {{ $label }}
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('priorite')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class='bx bx-paragraph mr-1'></i> Description détaillée
                </label>
                <textarea name="description" id="description" rows="5" required
                    placeholder="Décrivez précisément le problème rencontré..."
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 flex items-center justify-end gap-4">
                <a href="{{ route('employee.workorders.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition">
                    Annuler
                </a>
                
                <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <i class='bx bx-send'></i>
                    Envoyer la demande
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
