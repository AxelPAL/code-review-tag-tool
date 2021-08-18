<?php

namespace App\Contracts\Services;

interface SettingsServiceInterface
{
    public function getBitbucketClientId(): ?string;

    public function getBitbucketClientSecret(): ?string;

    public function getBitbucketRequestsUserId(): ?int;
}
