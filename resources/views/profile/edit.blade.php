<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Profile') }}
            </h2>
            <button class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300">
                {{ __('Enregistre les modifcations') }}
            </button>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Profile Information -->
            <div class="p-6 bg-white shadow-md sm:rounded-lg transition duration-300 hover:shadow-lg">
                <div class="flex items-center mb-4">
                    <x-heroicon-o-user class="h-6 w-6 text-blue-500 mr-2" />
                    <h3 class="text-lg font-semibold text-gray-700">{{ __('Mise à jour de vos Informations') }}</h3>
                </div>
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Update Password -->
            <div class="p-6 bg-white shadow-md sm:rounded-lg transition duration-300 hover:shadow-lg">
                <div class="flex items-center mb-4">
                    <x-heroicon-o-lock-closed class="h-6 w-6 text-green-500 mr-2" />
                    <h3 class="text-lg font-semibold text-gray-700">{{ __('Changer de Password') }}</h3>
                </div>
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account -->
            <div class="p-6 bg-white shadow-md sm:rounded-lg transition duration-300 hover:shadow-lg">
                <div class="flex items-center mb-4">
                    <x-heroicon-o-trash class="h-6 w-6 text-red-500 mr-2" />
                    <h3 class="text-lg font-semibold text-gray-700">{{ __('Supprimer le Compte') }}</h3>
                </div>
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
