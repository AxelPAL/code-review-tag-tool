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
        $this->mount();
        $this->userBitbucketSecrets->client_id = $this->clientId;
        $this->userBitbucketSecrets->client_secret = $this->clientSecret;
        return $this->getUserBitbucketSecretsRepository()->save($this->userBitbucketSecrets);
    }

    public function mount(): void
    {
        $bitbucketService = $this->getUserBitbucketSecretsRepository();
        $this->userBitbucketSecrets = $bitbucketService->findByUserId(auth()->id());
    }

    public function getUserBitbucketSecretsRepository(): UserBitbucketSecretsRepository
    {
        return resolve(UserBitbucketSecretsRepository::class);
    }

    public function render(): Factory|View|Application
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
