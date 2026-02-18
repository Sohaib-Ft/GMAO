@extends('layouts.app')

@section('title', 'Créer un Plan de Maintenance')
@section('header', 'Nouveau Plan de Maintenance')


@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
    @if($errors->any())
        <div class="mb-4 text-red-600 font-medium p-4 bg-red-50 rounded-xl">
            <div class="flex items-center mb-2">
                <i class='bx bx-error-circle mr-2 text-xl'></i>
                <strong>Erreurs de validation :</strong>
            </div>
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-8 flex items-center justify-between border-b border-gray-100 pb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Créer un Plan </h3>
            <p class="text-gray-500 text-sm mt-1">Créez un plan de maintenance préventive pour vos équipements</p>
        </div>
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-blue-100 text-blue-600">
            <i class='bx bx-calendar-plus text-2xl'></i>
        </div>
    </div>

    <form method="POST" action="{{ route('maintenance-plans.store') }}" class="space-y-8">
        @csrf

        <!-- SECTION 1: ÉQUIPEMENT -->
        <div>
            <div class="mb-5 text-blue-600 uppercase text-xs font-bold tracking-widest border-b border-blue-100 pb-2 flex items-center">
                <i class='bx bx-wrench mr-2 text-lg'></i> Équipement
            </div>
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Equipment Selection with Alpine.js -->
                <div x-data="{
                    open: false,
                    search: '',
                    selectedId: '{{ old('equipement_id') }}',
                    selectedName: '',
                    equipments: [],
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
                        this.selectedName = e.nom;
                        this.search = this.selectedName;
                        this.open = false;
                    },
                    async loadEquipments() {
                        if (this.equipments.length === 0) {
                            const response = await fetch('/api/equipments/search?q=');
                            this.equipments = await response.json();
                        }
                    },
                    init() {
                        if (this.selectedId) {
                            fetch(`/api/equipments/${this.selectedId}`)
                                .then(r => r.json())
                                .then(eq => {
                                    this.selectedName = eq.nom;
                                    this.search = this.selectedName;
                                });
                        }
                    }
                }" x-init="init()" class="relative">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class='bx bx-wrench mr-1 text-blue-500'></i> Équipement <span class="text-red-500">*</span>
                    </label>
                    
                    <input type="hidden" name="equipement_id" :value="selectedId" required>
                    
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-blue-500 transition-colors">
                            <i class='bx bx-search text-xl'></i>
                        </span>
                        <input type="text" 
                               x-model="search"
                               @focus="open = true; loadEquipments(); if(selectedId) search = ''"
                               @click.away="open = false; if(selectedId && search === '') search = selectedName"
                               placeholder="Commencez à taper le nom ou le code d'inventaire..."
                               class="w-full pl-11 pr-10 py-3.5 bg-gray-50 border-2 border-blue-500 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white shadow-sm transition-all outline-none font-medium text-gray-700"
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
                         class="absolute z-50 w-full mt-2 bg-white border-2 border-blue-500 rounded-2xl shadow-2xl max-h-64 overflow-y-auto overflow-x-hidden py-2">
                        
                        <template x-for="eq in filteredEquipments" :key="eq.id">
                            <button type="button" 
                                    @click="select(eq)"
                                    class="w-full text-left px-5 py-3 hover:bg-blue-50 transition flex items-center gap-4 border-b border-gray-50 last:border-0 group">
                                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center shadow-sm group-hover:bg-blue-600 group-hover:text-white transition-all">
                                    <i class='bx bx-wrench text-xl'></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors" x-text="eq.nom"></span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded font-mono uppercase tracking-tighter" x-text="eq.code"></span>
                                        <span class="text-[10px] text-gray-400 italic" x-text="eq.localisation || 'N/A'"></span>
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
            </div>
        </div>

        <!-- SECTION 2: RÉCURRENCE RRULE -->
        <div>
            <div class="mb-5 text-blue-600 uppercase text-xs font-bold tracking-widest border-b border-blue-100 pb-2 flex items-center">
                <i class='bx bx-sync mr-2 text-lg'></i> Récurrence
            </div>
            
            <x-rrule-generator 
                name="rrule" 
                :value="old('rrule', '')"
                label="Règle de Récurrence (RRULE)"
                :required="true"
            />

            @error('rrule') 
                <p class="text-red-600 text-xs mt-2"><i class='bx bx-error-circle'></i> {{ $message }}</p> 
            @enderror
        </div>

        <!-- SECTION 3: TYPE & FRÉQUENCE -->
        <div>
            <div class="mb-5 text-blue-600 uppercase text-xs font-bold tracking-widest border-b border-blue-100 pb-2 flex items-center">
                <i class='bx bx-cog mr-2 text-lg'></i> Configuration Supplémentaire
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Type (Hidden, always Preventive as requested) -->
                <input type="hidden" name="type" value="preventive">

                <!-- Technicien Assigné -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Technicien Assigné
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class='bx bx-user'></i>
                        </span>
                        <select name="technicien_id" 
                            class="pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">— Aucun technicien (Non assigné) —</option>
                            @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}" {{ old('technicien_id') == $tech->id ? 'selected' : '' }}>
                                    {{ $tech->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('technicien_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Hidden Statut actif -->
                <input type="hidden" name="statut" value="actif">
            </div>
        </div>

      
                       
                  

        <!-- Buttons -->
        <div class="flex items-center justify-end pt-6 border-t border-gray-100 space-x-4">
            <a href="{{ route('maintenance-plans.index') }}" class="px-6 py-2.5 rounded-xl text-gray-500 font-bold hover:bg-gray-100 transition">
                Annuler
            </a>

            <button type="submit" class="px-8 py-2.5 bg-[#1e293b] text-white rounded-xl font-bold shadow-lg hover:bg-gray-800 transition transform hover:-translate-y-0.5 flex items-center">
                <i class='bx bx-save mr-2'></i> Créer le Plan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('equipment-search');
    const hiddenInput = document.getElementById('equipement_id');
    const dropdown = document.getElementById('autocomplete-dropdown');
    let debounceTimer;
    let selectedIndex = -1;
    let equipmentData = [];
    let allEquipments = []; // Store all equipments

    // Load all equipments on focus/click
    searchInput.addEventListener('focus', function() {
        if (equipmentData.length === 0) {
            loadAllEquipments();
        } else {
            dropdown.classList.remove('hidden');
        }
    });

    searchInput.addEventListener('click', function() {
        if (equipmentData.length === 0) {
            loadAllEquipments();
        } else {
            dropdown.classList.remove('hidden');
        }
    });

    function loadAllEquipments() {
        console.log('Loading all equipments...');
        fetch('/api/equipments/search?q=')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                console.log('All equipments loaded:', data);
                allEquipments = data;
                equipmentData = data;
                renderDropdown(data);
            })
            .catch(error => {
                console.error('Error loading equipments:', error);
                dropdown.innerHTML = '<div class="autocomplete-item text-red-500 text-sm">Erreur de chargement</div>';
                dropdown.classList.remove('hidden');
            });
    }

    // Search equipment via AJAX
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        // Clear hidden input when user types
        if (hiddenInput.value) {
            hiddenInput.value = '';
        }
        
        clearTimeout(debounceTimer);
        
        // If empty, show all
        if (query.length === 0) {
            equipmentData = allEquipments;
            renderDropdown(allEquipments);
            return;
        }

        debounceTimer = setTimeout(() => {
            console.log('Searching for:', query);
            fetch(`/api/equipments/search?q=${encodeURIComponent(query)}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    console.log('Results:', data);
                    equipmentData = data;
                    renderDropdown(data);
                })
                .catch(error => {
                    console.error('Error fetching equipment:', error);
                    dropdown.innerHTML = '<div class="autocomplete-item text-red-500 text-sm">Erreur de chargement</div>';
                    dropdown.classList.remove('hidden');
                });
        }, 300);
    });

    function renderDropdown(items) {
        if (items.length === 0) {
            dropdown.innerHTML = '<div class="autocomplete-item text-gray-500 text-sm text-center">Aucun équipement trouvé</div>';
            dropdown.classList.remove('hidden');
            return;
        }

        dropdown.innerHTML = items.map((item, index) => `
            <div class="autocomplete-item" data-index="${index}" data-id="${item.id}">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center mr-3 flex-shrink-0">
                        <i class='bx bx-wrench text-blue-600 text-xl'></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-bold text-gray-900 text-base">${item.nom}</div>
                        <div class="text-xs text-gray-500">${item.code}</div>
                        <div class="text-xs text-gray-400 italic">${item.localisation || 'N/A'}</div>
                    </div>
                </div>
            </div>
        `).join('');

        dropdown.classList.remove('hidden');
        selectedIndex = -1;

        // Add click event to items
        dropdown.querySelectorAll('.autocomplete-item').forEach((item, idx) => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Item clicked:', idx);
                selectEquipment(idx);
            });
        });
    }

    function selectEquipment(index) {
        console.log('Selecting equipment at index:', index);
        if (index >= 0 && index < equipmentData.length) {
            const selected = equipmentData[index];
            searchInput.value = `${selected.nom}`;
            hiddenInput.value = selected.id;
            dropdown.classList.add('hidden');
            selectedIndex = -1;
            console.log('Equipment selected:', selected);
        }
    }

    // Keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        const items = dropdown.querySelectorAll('.autocomplete-item');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
            updateSelection(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, 0);
            updateSelection(items);
        } else if (e.key === 'Enter' && selectedIndex >= 0) {
            e.preventDefault();
            selectEquipment(selectedIndex);
        } else if (e.key === 'Escape') {
            dropdown.classList.add('hidden');
        }
    });

    function updateSelection(items) {
        items.forEach((item, index) => {
            if (index === selectedIndex) {
                item.classList.add('selected');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('selected');
            }
        });
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const container = document.getElementById('equipment-autocomplete-container');
        if (container && !container.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Pre-fill if old value exists
    @if(old('equipement_id'))
        fetch(`/api/equipments/{{ old('equipement_id') }}`)
            .then(response => response.json())
            .then(equipment => {
                searchInput.value = `${equipment.nom}`;
                hiddenInput.value = equipment.id;
            })
            .catch(error => console.error('Error loading equipment:', error));
    @endif
});
</script>
@endpush
