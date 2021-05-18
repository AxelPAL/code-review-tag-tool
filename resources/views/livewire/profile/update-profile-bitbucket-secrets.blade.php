<x-jet-form-section submit="saveSecrets">
    <x-slot name="title">
        {{ __('Bitbucket Secrets') }}
    </x-slot>

    <x-slot name="description">
        <p>
            {{ __('Update your Bitbucket\'s client_id and client_secret.') }}
        </p>
        <p>
            Here is a tutorial how to get them:
            <a href="https://support.atlassian.com/bitbucket-cloud/docs/use-oauth-on-bitbucket-cloud/">link</a>
        </p>
    </x-slot>

    <x-slot name="form">
        <!-- client_id -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="clientId" value="{{ __('client_id') }}" />
            <x-jet-input id="clientId" type="text" class="mt-1 block w-full" wire:model.defer="clientId" />
            <x-jet-input-error for="clientId" class="mt-2" />
        </div>

        <!-- client_secret -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="clientSecret" value="{{ __('client_secret') }}" />
            <x-jet-input id="clientSecret" class="mt-1 block w-full" wire:model.defer="clientSecret" />
            <x-jet-input-error for="clientSecret" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            {{ __('Saved.') }}
        </x-jet-action-message>

        <x-jet-button wire:loading.attr="disabled" wire:submit="saveSecrets">
            {{ __('Save') }}
        </x-jet-button>
    </x-slot>
</x-jet-form-section>
