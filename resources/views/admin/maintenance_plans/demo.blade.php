@extends('layouts.app')

@section('title', 'Démonstration - Générateur RRULE')

@section('content')
<div class="max-w-6xl mx-auto py-8 space-y-8">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 rounded-xl p-8">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-indigo-600 rounded-xl flex items-center justify-center">
                <i class='bx bx-calendar-check text-white text-3xl'></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Générateur de Récurrence RRULE</h1>
                <p class="text-gray-600 mt-1">Créez des règles de récurrence sans connaître la syntaxe technique</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Main Demo -->
        <div class="lg:col-span-2">
            <form class="space-y-6">
                <!-- Title -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-1">Configurez votre Récurrence</h2>
                    <p class="text-gray-600 text-sm">Utilisez l'interface ci-dessous pour générer une règle RRULE</p>
                </div>

                <!-- Composant RRULE -->
                <x-rrule-generator 
                    name="demo_rrule"
                    :value="'FREQ=WEEKLY;BYDAY=MO,WE,FR'"
                    label="Ma Règle de Récurrence"
                    :required="false"
                />

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
                    <i class='bx bx-info-circle text-blue-600 text-lg mt-0.5 flex-shrink-0'></i>
                    <div class="text-sm text-blue-700">
                        <p class="font-medium mb-1">À propos de la RRULE</p>
                        <p>La chaîne RRULE (Recurrence Rule) est un standard de la RFC 5545 utilisé par les calendriers (iCalendar). Elle permet de définir des patterns de récurrence complexes de façon compacte et standardisée.</p>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar - Examples -->
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm sticky top-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Exemples Prédéfinis</h3>
                
                <div class="space-y-3">
                    <button type="button" 
                            onclick="loadExample('FREQ=DAILY')"
                            class="w-full text-left p-3 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-indigo-300 transition text-sm">
                        <p class="font-medium text-gray-800"><i class='bx bx-sun mr-2'></i>Quotidien</p>
                        <p class="text-gray-500 text-xs mt-1">Se répète chaque jour</p>
                    </button>

                    <button type="button"
                            onclick="loadExample('FREQ=WEEKLY;BYDAY=MO,WE,FR')"
                            class="w-full text-left p-3 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-indigo-300 transition text-sm bg-indigo-50 border-indigo-200">
                        <p class="font-medium text-gray-800"><i class='bx bx-calendar mr-2'></i>Hebdo (L, M, V)</p>
                        <p class="text-gray-500 text-xs mt-1 font-mono text-blue-600">FREQ=WEEKLY;BYDAY=MO,WE,FR</p>
                    </button>

                    <button type="button"
                            onclick="loadExample('FREQ=DAILY;INTERVAL=2')"
                            class="w-full text-left p-3 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-indigo-300 transition text-sm">
                        <p class="font-medium text-gray-800"><i class='bx bx-time mr-2'></i>Tous les 2 Jours</p>
                        <p class="text-gray-500 text-xs mt-1">INTERVAL=2</p>
                    </button>

                    <button type="button"
                            onclick="loadExample('FREQ=MONTHLY;BYMONTHDAY=1')"
                            class="w-full text-left p-3 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-indigo-300 transition text-sm">
                        <p class="font-medium text-gray-800"><i class='bx bx-calendar-heart mr-2'></i>1er du Mois</p>
                        <p class="text-gray-500 text-xs mt-1">BYMONTHDAY=1</p>
                    </button>

                    <button type="button"
                            onclick="loadExample('FREQ=MONTHLY;BYDAY=1MO')"
                            class="w-full text-left p-3 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-indigo-300 transition text-sm">
                        <p class="font-medium text-gray-800"><i class='bx bx-calendar-check mr-2'></i>1er Lundi Mois</p>
                        <p class="text-gray-500 text-xs mt-1">BYDAY=1MO</p>
                    </button>

                    <button type="button"
                            onclick="loadExample('FREQ=YEARLY')"
                            class="w-full text-left p-3 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-indigo-300 transition text-sm">
                        <p class="font-medium text-gray-800"><i class='bx bx-calendar-year mr-2'></i>Annuellement</p>
                        <p class="text-gray-500 text-xs mt-1">Se répète chaque année</p>
                    </button>

                    <button type="button"
                            onclick="loadExample('FREQ=MONTHLY;INTERVAL=3;BYMONTHDAY=15')"
                            class="w-full text-left p-3 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-indigo-300 transition text-sm">
                        <p class="font-medium text-gray-800"><i class='bx bx-calendar-alt mr-2'></i>Trimestriel</p>
                        <p class="text-gray-500 text-xs mt-1">INTERVAL=3 BYMONTHDAY=15</p>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Knowledge Base -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- RRULE Syntax -->
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class='bx bx-code mr-2 text-indigo-600'></i> Syntaxe RRULE
            </h3>
            
            <div class="space-y-4 text-sm">
                <div>
                    <p class="font-mono bg-gray-50 p-2 rounded border border-gray-200 text-gray-800 mb-2">
                        FREQ=DAILY|WEEKLY|MONTHLY|YEARLY
                    </p>
                    <p class="text-gray-600">Fréquence requise</p>
                </div>

                <div>
                    <p class="font-mono bg-gray-50 p-2 rounded border border-gray-200 text-gray-800 mb-2">
                        INTERVAL=n
                    </p>
                    <p class="text-gray-600">Intervalle (optionnel, défaut 1)</p>
                </div>

                <div>
                    <p class="font-mono bg-gray-50 p-2 rounded border border-gray-200 text-gray-800 mb-2">
                        BYDAY=MO,TU,WE,TH,FR,SA,SU
                    </p>
                    <p class="text-gray-600">Jours (pour WEEKLY)</p>
                </div>

                <div>
                    <p class="font-mono bg-gray-50 p-2 rounded border border-gray-200 text-gray-800 mb-2">
                        BYMONTHDAY=1-31
                    </p>
                    <p class="text-gray-600">Jour du mois (pour MONTHLY)</p>
                </div>

                <div>
                    <p class="font-mono bg-gray-50 p-2 rounded border border-gray-200 text-gray-800 mb-2">
                        BYDAY=±nDOW
                    </p>
                    <p class="text-gray-600">Position relative (ex: 2MO = 2e lundi)</p>
                </div>
            </div>
        </div>

        <!-- Jours de la Semaine -->
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class='bx bx-calendar mr-2 text-indigo-600'></i> Codes Jours
            </h3>
            
            <div class="grid grid-cols-2 gap-2 text-sm">
                <div class="bg-blue-50 p-2 rounded border border-blue-200">
                    <p class="font-mono font-bold text-blue-600">MO</p>
                    <p class="text-gray-600">Lundi</p>
                </div>
                <div class="bg-blue-50 p-2 rounded border border-blue-200">
                    <p class="font-mono font-bold text-blue-600">TU</p>
                    <p class="text-gray-600">Mardi</p>
                </div>
                <div class="bg-blue-50 p-2 rounded border border-blue-200">
                    <p class="font-mono font-bold text-blue-600">WE</p>
                    <p class="text-gray-600">Mercredi</p>
                </div>
                <div class="bg-blue-50 p-2 rounded border border-blue-200">
                    <p class="font-mono font-bold text-blue-600">TH</p>
                    <p class="text-gray-600">Jeudi</p>
                </div>
                <div class="bg-blue-50 p-2 rounded border border-blue-200">
                    <p class="font-mono font-bold text-blue-600">FR</p>
                    <p class="text-gray-600">Vendredi</p>
                </div>
                <div class="bg-blue-50 p-2 rounded border border-blue-200">
                    <p class="font-mono font-bold text-blue-600">SA</p>
                    <p class="text-gray-600">Samedi</p>
                </div>
                <div class="bg-blue-50 p-2 rounded border border-blue-200">
                    <p class="font-mono font-bold text-blue-600">SU</p>
                    <p class="text-gray-600">Dimanche</p>
                </div>
                <div class="bg-gray-50 p-2 rounded border border-gray-200">
                    <p class="font-mono font-bold text-gray-600">-1</p>
                    <p class="text-gray-600">Dernier</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Use Cases -->
    <div class="bg-white border border-gray-200 rounded-xl p-8">
        <h3 class="text-lg font-bold text-gray-800 mb-6">📚 Cas d'Usage Réels</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="border border-gray-200 rounded-lg p-4">
                <p class="font-bold text-gray-800 mb-2">🧹 Nettoyage</p>
                <p class="text-sm text-gray-600 mb-3">Chaque lundi et vendredi</p>
                <p class="font-mono text-xs bg-gray-50 p-2 rounded text-gray-800">FREQ=WEEKLY;BYDAY=MO,FR</p>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <p class="font-bold text-gray-800 mb-2">🔧 Service</p>
                <p class="text-sm text-gray-600 mb-3">Tous les 30 jours</p>
                <p class="font-mono text-xs bg-gray-50 p-2 rounded text-gray-800">FREQ=DAILY;INTERVAL=30</p>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <p class="font-bold text-gray-800 mb-2">📋 Inspection</p>
                <p class="text-sm text-gray-600 mb-3">1er mardi du mois</p>
                <p class="font-mono text-xs bg-gray-50 p-2 rounded text-gray-800">FREQ=MONTHLY;BYDAY=1TU</p>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <p class="font-bold text-gray-800 mb-2">🎯 Révision</p>
                <p class="text-sm text-gray-600 mb-3">Annuel (15 décembre)</p>
                <p class="font-mono text-xs bg-gray-50 p-2 rounded text-gray-800">FREQ=YEARLY</p>
            </div>
        </div>
    </div>

    <!-- Resources -->
    <div class="border-t border-gray-200 pt-8 mt-8">
        <h3 class="text-lg font-bold text-gray-800 mb-4">📖 Ressources</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ asset('docs/RRULE_GENERATOR.md') }}" class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <i class='bx bx-file text-indigo-600 text-xl flex-shrink-0 mt-1'></i>
                <div>
                    <p class="font-bold text-gray-800">Documentation Complète</p>
                    <p class="text-sm text-gray-600">Guide détaillé avec exemples</p>
                </div>
            </a>

            <a href="{{ asset('docs/RRULE_INTEGRATION_GUIDE.md') }}" class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <i class='bx bx-wrench text-indigo-600 text-xl flex-shrink-0 mt-1'></i>
                <div>
                    <p class="font-bold text-gray-800">Guide d'Intégration</p>
                    <p class="text-sm text-gray-600">Instructions de déploiement</p>
                </div>
            </a>

            <a href="https://tools.ietf.org/html/rfc5545" target="_blank" class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <i class='bx bx-book text-indigo-600 text-xl flex-shrink-0 mt-1'></i>
                <div>
                    <p class="font-bold text-gray-800">RFC 5545</p>
                    <p class="text-sm text-gray-600">Spécification iCalendar (externe)</p>
                </div>
            </a>
        </div>
    </div>
</div>

<script>
function loadExample(rrule) {
    // Mettre à jour le composant Alpine.js
    // Note: Cette fonction nécessite que le composant soit accessible via Alpine
    alert('Exemple: ' + rrule);
    // En production, vous pouvez utiliser Alpine.store() ou Alpine.dispatch()
}
</script>
@endsection
