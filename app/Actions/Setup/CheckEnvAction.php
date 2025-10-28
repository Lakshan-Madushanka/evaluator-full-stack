<?php

namespace App\Actions\Setup;

use Illuminate\Support\Facades\Artisan;

class CheckEnvAction
{
    public function execute(): array
    {
        if (! $this->checkEnvFileExists()) {
            return [
                'is_exists' => false,
            ];
        }

        return [
            'is_exists' => true,
            ...$this->getData(),
            'is_passed' => app()->isProduction() && ! app()->hasDebugModeEnabled(),
        ];
    }

    private function checkEnvFileExists(): bool
    {
        return file_exists(base_path('.env'));
    }

    public function getData(): array
    {
        return [
            'app' => [
                ['key' => 'APP_ENV', 'value' => app()->environment(), 'refer' => 'Project Environment'],
                ['key' => 'APP_DEBUG', 'value' => app()->hasDebugModeEnabled(), 'refer' => 'Debug Mode'],
                ['key' => 'APP_URL', 'value' => config('app.url'), 'refer' => 'Domain'],
                ['key' => 'APP_NAME', 'value' => config('app.name'), 'refer' => 'Site Name'],
            ],
        ];
    }

    public function generateAppKey(): bool
    {
        $status = Artisan::call('key:generate', ['--force' => true]);

        if ($status !== 0) {
            return false;
        }

        return true;
    }
}
