<?php

namespace App\Services;

use App\Contracts\Services\SettingsServiceInterface;
use App\Models\Setting;
use App\Repositories\SettingsRepository;

class SettingsService implements SettingsServiceInterface
{
    private SettingsRepository $settingsRepository;

    public function __construct(SettingsRepository $settingsRepository)
    {
        $this->settingsRepository = $settingsRepository;
    }

    public function getBitbucketClientId(): ?string
    {
        return $this->settingsRepository->findById(Setting::BITBUCKET_CLIENT_ID_ID)?->value;
    }

    public function getBitbucketClientSecret(): ?string
    {
        return $this->settingsRepository->findById(Setting::BITBUCKET_CLIENT_SECRET_ID)?->value;
    }

    public function getBitbucketRequestsUserId(): ?int
    {
        return (int)$this->settingsRepository->findById(Setting::BITBUCKET_REQUESTS_USER_ID)?->value;
    }
}
