<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Vous avez perdu votre mot de passe ? Remplissez ce formulaire et notre administrateur vous enverra un nouveau mot de passe temporaire par e-mail après vérification.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    @if(session('error'))
        <div class="mb-4 text-sm text-red-600">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('reclamation.store') }}">
        @csrf

        <!-- Nom -->
        <div>
            <x-input-label for="nom" :value="__('Nom')" />
            <x-text-input id="nom" class="block mt-1 w-full" type="text" name="nom" :value="old('nom')" required autofocus />
            <x-input-error :messages="$errors->get('nom')" class="mt-2" />
        </div>

        <!-- Prénom removed (stored in users table only) -->



        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email du compte')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Message additionnel -->
        <div class="mt-4">
            <x-input-label for="message" :value="__('Message (Optionnel)')" />
            <textarea id="message" name="message" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('message') }}</textarea>
            <x-input-error :messages="$errors->get('message')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6 space-x-4">
            <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:underline">
                {{ __('Retour au login') }}
            </a>
            <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                {{ __('Envoyer la demande') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
