<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Repositories\SettingsRepository;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(SettingsRepository $settingsRepository): void
    {
        foreach (Setting::ALL_SETTINGS as $settingId => $settingTitle) {
            $modelExists = $settingsRepository->existsById($settingId);
            if (!$modelExists) {
                $model = new Setting();
                $model->id = $settingId;
                $model->title = $settingTitle;
                $settingsRepository->save($model);
            }
        }
    }
}
