<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-black" />
            <x-text-input 
                id="email" 
                class="block mt-1 w-full" 
                type="email" 
                name="email" 
                :value="old('email')" 
                required 
                autofocus 
                autocomplete="username" 
                placeholder="Entrez votre email" 
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Mot de passe')" />
            <x-text-input 
                id="password" 
                class="block mt-1 w-full"
                type="password"
                name="password"
                required 
                autocomplete="current-password" 
                placeholder="Entrez votre mot de passe"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>



        <!-- Mot de passe oubliee -->
        <div class="block mt-4">
            <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">
                {{ __('Mot de passe oublié ?') }}
            </a>
        </div>

        <!-- Login Button -->
        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-3 primary-btn-custom">
                {{ __('Se connecter') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

<style>
.primary-btn-custom {
    background-color: rgb(59, 130, 246);
    color: white;
    transition: background-color 0.2s;
}
.primary-btn-custom:hover {
    background-color: rgb(37, 99, 235);
}
</style>
