@props(['name' => 'rrule', 'value' => '', 'label' => 'Récurrence', 'required' => false])

<div x-data="rruleGenerator(@js($value))" class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm">
    <label class="block text-sm font-bold text-gray-700 mb-4">{{ $label }}</label>
    
    <input type="hidden" name="{{ $name }}" :value="rruleString">

    <!-- Fréquence Principale -->
    <div class="flex flex-wrap gap-2 mb-6">
        <template x-for="freq in frequencies" :key="freq.value">
            <button type="button" 
                @click="frequency = freq.value; updateRRule()"
                :class="frequency === freq.value ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-50 text-gray-600 hover:bg-gray-100'"
                class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                <span x-text="freq.label"></span>
            </button>
        </template>
    </div>

    <!-- Options Hebdomadaires -->
    <div x-show="frequency === 'WEEKLY'" class="space-y-4 animate-fadeIn">
        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Sélectionner les jours</span>
        <div class="flex flex-wrap gap-2">
            <template x-for="day in weekDays" :key="day.value">
                <button type="button" 
                    @click="toggleDay(day.value)"
                    :class="selectedDays.includes(day.value) ? 'bg-blue-100 text-blue-700 border-blue-200' : 'bg-white text-gray-400 border-gray-100'"
                    class="h-10 w-10 rounded-full border-2 text-[10px] font-bold flex items-center justify-center transition-all hover:scale-105">
                    <span x-text="day.label"></span>
                </button>
            </template>
        </div>
    </div>

    <!-- Options Mensuelles -->
    <div x-show="frequency === 'MONTHLY'" class="space-y-4 animate-fadeIn">
        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100">
            <select x-model="monthlyType" @change="updateRRule()" class="bg-transparent border-none text-sm font-bold text-gray-700 focus:ring-0">
                <option value="DOM">Le [X] du mois</option>
                <option value="DOW">Le dernier...</option>
            </select>
            
            <template x-if="monthlyType === 'DOM'">
                <input type="number" x-model="dayOfMonth" @input="updateRRule()" min="1" max="31" class="w-16 bg-white border-gray-200 rounded-lg text-sm font-bold py-1">
            </template>
            
            <template x-if="monthlyType === 'DOW'">
                <select x-model="lastDayOfWeek" @change="updateRRule()" class="bg-white border-gray-200 rounded-lg text-sm font-bold py-1">
                    <option value="MO">Lundi</option>
                    <option value="TU">Mardi</option>
                    <option value="WE">Mercredi</option>
                    <option value="TH">Jeudi</option>
                    <option value="FR">Vendredi</option>
                    <option value="SA">Samedi</option>
                    <option value="SU">Dimanche</option>
                </select>
            </template>
        </div>
    </div>

    <!-- Résumé Lisible -->
    <div class="mt-8 pt-6 border-t border-gray-50 flex items-start gap-3">
        <div class="p-2 bg-blue-50 text-blue-600 rounded-lg mt-1">
            <i class='bx bx-info-circle text-lg'></i>
        </div>
        <div>
            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Résumé de planification</span>
            <p class="text-sm font-bold text-gray-900" x-text="summary"></p>
            <code class="text-[10px] text-gray-300 mt-1 block" x-text="rruleString"></code>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('rruleGenerator', (initialValue) => ({
        frequency: 'WEEKLY',
        selectedDays: [], // MO, TU, WE, TH, FR, SA, SU
        monthlyType: 'DOM',
        dayOfMonth: 1,
        lastDayOfWeek: 'FR',
        rruleString: initialValue || '',
        
        frequencies: [
            { label: 'Quotidien', value: 'DAILY' },
            { label: 'Hebdo', value: 'WEEKLY' },
            { label: 'Mensuel', value: 'MONTHLY' }
        ],
        
        weekDays: [
            { label: 'Lun', value: 'MO' },
            { label: 'Mar', value: 'TU' },
            { label: 'Mer', value: 'WE' },
            { label: 'Jeu', value: 'TH' },
            { label: 'Ven', value: 'FR' },
            { label: 'Sam', value: 'SA' },
            { label: 'Dim', value: 'SU' }
        ],

        init() {
            if (this.rruleString) {
                this.parseRRule(this.rruleString);
            } else {
                this.updateRRule();
            }
        },

        toggleDay(day) {
            if (this.selectedDays.includes(day)) {
                this.selectedDays = this.selectedDays.filter(d => d !== day);
            } else {
                this.selectedDays.push(day);
            }
            this.updateRRule();
        },

        updateRRule() {
            let parts = [`FREQ=${this.frequency}`];
            
            if (this.frequency === 'WEEKLY' && this.selectedDays.length > 0) {
                parts.push(`BYDAY=${this.selectedDays.join(',')}`);
            } else if (this.frequency === 'MONTHLY') {
                if (this.monthlyType === 'DOM') {
                    parts.push(`BYMONTHDAY=${this.dayOfMonth}`);
                } else {
                    parts.push(`BYDAY=-1${this.lastDayOfWeek}`);
                }
            }
            
            this.rruleString = parts.join(';');
        },

        get summary() {
            const dayNames = { MO: 'Lundis', TU: 'Mardis', WE: 'Mercredis', TH: 'Jeudis', FR: 'Vendredis', SA: 'Samedis', SU: 'Dimanches' };
            const daySingle = { MO: 'Lundi', TU: 'Mardi', WE: 'Mercredi', TH: 'Jeudi', FR: 'Vendredi', SA: 'Samedi', SU: 'Dimanche' };
            
            if (this.frequency === 'DAILY') return 'Répéter chaque jour';
            
            if (this.frequency === 'WEEKLY') {
                if (this.selectedDays.length === 0) return 'Sélectionnez au moins un jour';
                return 'Répéter tous les ' + this.selectedDays.map(d => dayNames[d]).join(', ');
            }
            
            if (this.frequency === 'MONTHLY') {
                if (this.monthlyType === 'DOM') return `Répéter le ${this.dayOfMonth} de chaque mois`;
                return `Répéter le dernier ${daySingle[this.lastDayOfWeek]} du mois`;
            }
            
            return 'Règle en cours de définition...';
        },

        parseRRule(str) {
            const parts = str.split(';');
            parts.forEach(p => {
                const [key, val] = p.split('=');
                if (key === 'FREQ') this.frequency = val;
                if (key === 'BYDAY') {
                    if (val.startsWith('-1')) {
                        this.monthlyType = 'DOW';
                        this.lastDayOfWeek = val.replace('-1', '');
                    } else {
                        this.selectedDays = val.split(',');
                    }
                }
                if (key === 'BYMONTHDAY') {
                    this.monthlyType = 'DOM';
                    this.dayOfMonth = val;
                }
            });
        }
    }));
});
</script>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
}
</style>
