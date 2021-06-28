<?php

namespace App\Repositories;

use App\Models\Setting;

class SettingsRepository
{
    public function save(Setting $setting): bool
    {
        return $setting->save();
    }

    public function findById(int $id): ?Setting
    {
        return Setting::whereId($id)->first();
    }

    public function existsById(int $id): bool
    {
        return Setting::whereId($id)->exists();
    }
}
