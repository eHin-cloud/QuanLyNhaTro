<?php

namespace App\Casts;

use App\Support\SensitiveData;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class Aes256GcmEncrypted implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $payload = json_decode((string) $value, true);

        if (!is_array($payload) || ($payload['alg'] ?? null) !== 'AES-256-GCM') {
            return (string) $value;
        }

        $iv = base64_decode($payload['iv'] ?? '', true);
        $tag = base64_decode($payload['tag'] ?? '', true);
        $ciphertext = base64_decode($payload['data'] ?? '', true);

        if ($iv === false || $tag === false || $ciphertext === false) {
            throw new \RuntimeException("Invalid encrypted payload for {$key}.");
        }

        $aad = $this->aad($model, $key);
        $plaintext = openssl_decrypt(
            $ciphertext,
            'aes-256-gcm',
            SensitiveData::encryptionKey(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            $aad
        );

        if ($plaintext === false) {
            throw new \RuntimeException("Unable to decrypt sensitive attribute {$key}.");
        }

        return $plaintext;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value) && $this->isEncryptedPayload($value)) {
            return $value;
        }

        $iv = random_bytes(12);
        $tag = '';
        $ciphertext = openssl_encrypt(
            (string) $value,
            'aes-256-gcm',
            SensitiveData::encryptionKey(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            $this->aad($model, $key)
        );

        if ($ciphertext === false) {
            throw new \RuntimeException("Unable to encrypt sensitive attribute {$key}.");
        }

        return json_encode([
            'v' => 1,
            'alg' => 'AES-256-GCM',
            'kid' => config('security.pii_key_id', 'app-key-v1'),
            'iv' => base64_encode($iv),
            'tag' => base64_encode($tag),
            'data' => base64_encode($ciphertext),
        ], JSON_THROW_ON_ERROR);
    }

    private function aad(Model $model, string $key): string
    {
        $tenantId = $model->getAttribute('tenant_id') ?? 'platform';

        return $model->getTable() . ':' . $tenantId . ':' . $key;
    }

    private function isEncryptedPayload(string $value): bool
    {
        $payload = json_decode($value, true);

        return is_array($payload) && ($payload['alg'] ?? null) === 'AES-256-GCM';
    }
}
