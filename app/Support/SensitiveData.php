<?php

namespace App\Support;

use Illuminate\Support\Facades\Config;

class SensitiveData
{
    public static function normalize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = mb_strtolower(preg_replace('/\s+/', '', trim($value)) ?? '');

        return $normalized === '' ? null : $normalized;
    }

    public static function blindIndex(?string $value): ?string
    {
        $normalized = self::normalize($value);

        if ($normalized === null) {
            return null;
        }

        return hash_hmac('sha256', $normalized, self::blindIndexKey());
    }

    public static function mask(?string $value, int $prefix = 4, int $suffix = 4): ?string
    {
        $normalized = self::normalize($value);

        if ($normalized === null) {
            return null;
        }

        $length = strlen($normalized);

        if ($length <= ($prefix + $suffix)) {
            return str_repeat('x', max(4, $length));
        }

        return substr($normalized, 0, $prefix) . 'xxxx' . substr($normalized, -$suffix);
    }

    public static function maskPhone(?string $value): ?string
    {
        return self::mask($value, 4, 3);
    }

    public static function maskBankAccount(?string $value): ?string
    {
        return self::mask($value, 3, 4);
    }

    public static function maskNationalId(?string $value): ?string
    {
        return self::mask($value, 4, 4);
    }

    public static function encryptionKey(): string
    {
        $configured = Config::get('security.pii_encryption_key');

        if (is_string($configured) && str_starts_with($configured, 'base64:')) {
            $key = base64_decode(substr($configured, 7), true);
        } elseif (is_string($configured) && $configured !== '') {
            $key = base64_decode($configured, true);
        } else {
            $appKey = Config::get('app.key');
            $key = is_string($appKey) && str_starts_with($appKey, 'base64:')
                ? base64_decode(substr($appKey, 7), true)
                : hash('sha256', (string) $appKey, true);
        }

        if (!is_string($key) || strlen($key) !== 32) {
            throw new \RuntimeException('PII encryption key must be 32 bytes after base64 decoding.');
        }

        return $key;
    }

    public static function blindIndexKey(): string
    {
        $configured = Config::get('security.pii_blind_index_key');

        if (is_string($configured) && str_starts_with($configured, 'base64:')) {
            $key = base64_decode(substr($configured, 7), true);
        } elseif (is_string($configured) && $configured !== '') {
            $key = base64_decode($configured, true);
        } else {
            $key = hash('sha256', 'blind-index:' . Config::get('app.key'), true);
        }

        if (!is_string($key) || strlen($key) < 32) {
            throw new \RuntimeException('PII blind index key must be at least 32 bytes after base64 decoding.');
        }

        return $key;
    }
}
