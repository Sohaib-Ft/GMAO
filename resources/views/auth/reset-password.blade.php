<x-guest-layout>
    <div class="mb-4">
        <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center mb-4">
            <i class='bx bx-lock-open text-2xl text-blue-600'></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Réinitialiser le mot de passe</h3>
        @if (!session('status'))
            <p class="text-gray-500 text-sm">
                Veuillez choisir un nouveau mot de passe sécurisé pour votre compte.
            </p>
        @endif
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('status'))
        <div class="mt-6">
            <a href="{{ route('login') }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl shadow-lg shadow-blue-100 transition-all font-bold">
                Se connecter avec le nouveau mot de passe
            </a>
        </div>
    @else
        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <input type="hidden" name="email" value="{{ $request->email }}">

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Nouveau mot de passe')" class="font-bold text-gray-700" />
                <x-text-input id="password" 
                    class="block mt-1 w-full border-gray-100 bg-gray-50 focus:bg-white transition-all rounded-xl py-3" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="new-password" 
                    placeholder="••••••••"
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" class="font-bold text-gray-700" />
                <x-text-input id="password_confirmation" 
                    class="block mt-1 w-full border-gray-100 bg-gray-50 focus:bg-white transition-all rounded-xl py-3"
                    type="password"
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password" 
                    placeholder="••••••••"
                />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-6">
                <x-primary-button class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl shadow-lg shadow-blue-100 transition-all font-bold w-full justify-center">
                    {{ __('Réinitialiser le mot de passe') }}
                </x-primary-button>
            </div>
        </form>
    @endif
</x-guest-layout>
