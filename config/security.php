<?php

return [
    'pii_key_id' => env('PII_KEY_ID', 'app-key-v1'),
    'pii_encryption_key' => env('PII_ENCRYPTION_KEY'),
    'pii_blind_index_key' => env('PII_BLIND_INDEX_KEY'),
    'document_disk' => env('SECURE_DOCUMENT_DISK', 'private_documents'),
    'presigned_url_ttl_seconds' => (int) env('SECURE_DOCUMENT_URL_TTL', 300),
];
