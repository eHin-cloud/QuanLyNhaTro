<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class SystemSettingsService
{
    protected string $filePath = 'settings.json';

    public function getAll(): array
    {
        if (!Storage::disk('local')->exists($this->filePath)) {
            return $this->getDefaults();
        }

        try {
            $data = json_decode(Storage::disk('local')->get($this->filePath), true);
            return array_merge($this->getDefaults(), $data ?: []);
        } catch (\Exception $e) {
            return $this->getDefaults();
        }
    }

    public function get(string $key, $default = null)
    {
        $settings = $this->getAll();
        return $settings[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $settings = $this->getAll();
        $settings[$key] = $value;
        Storage::disk('local')->put($this->filePath, json_encode($settings, JSON_PRETTY_PRINT));
    }

    public function setMany(array $data): void
    {
        $settings = array_merge($this->getAll(), $data);
        Storage::disk('local')->put($this->filePath, json_encode($settings, JSON_PRETTY_PRINT));
    }

    protected function getDefaults(): array
    {
        return [
            'presigned_url_ttl_seconds' => 300,
            'require_passkey' => true,
        ];
    }
}
