<?php

namespace App\Http\Livewire\Profile;

use App\Models\UserBitbucketSecrets;
use App\Repositories\UserBitbucketSecretsRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class UpdateProfileBitbucketSecrets extends Component
{
    public string $clientId = '';
    public string $clientSecret = '';

    private ?UserBitbucketSecrets $userBitbucketSecrets;

    public function saveSecrets(): bool
    {
        $result = false;
        $this->mount();
        if ($this->userBitbucketSecrets !== null) {
            $this->userBitbucketSecrets->client_id = $this->clientId;
            $this->userBitbucketSecrets->client_secret = $this->clientSecret;
            $result = $this->getUserBitbucketSecretsRepository()->save($this->userBitbucketSecrets);
        }
        return $result;
    }

    public function mount(): void
    {
        $bitbucketService = $this->getUserBitbucketSecretsRepository();
        if (auth()->id() !== null) {
            $this->userBitbucketSecrets = $bitbucketService->findByUserId((int)auth()->id());
        }
    }

    public function getUserBitbucketSecretsRepository(): UserBitbucketSecretsRepository
    {
        return resolve(UserBitbucketSecretsRepository::class);
    }

    public function render(): Factory | View | Application
    {
        $this->loadData();
        return view('livewire.profile.update-profile-bitbucket-secrets');
    }

    private function loadData(): void
    {
        if ($this->userBitbucketSecrets !== null) {
            $this->clientId = $this->userBitbucketSecrets->client_id;
            $this->clientSecret = $this->userBitbucketSecrets->client_secret;
        }
    }
}
