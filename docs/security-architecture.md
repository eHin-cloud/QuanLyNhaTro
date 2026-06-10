# SmartRoom/Renty Security Architecture

## Sensitive Data

Personal data protected by Decree 13/2023/ND-CP is encrypted at application level before database persistence.

Encrypted fields:

- `users.phone`
- `tenants.phone`
- `tenants.bank_account_no`
- `residents.phone`
- `residents.cccd`
- `resident_relatives.phone`
- `resident_relatives.cccd`
- `landlord_profiles.phone`
- `landlord_verification_requests.cccd_number`

Implementation:

- `App\Casts\Aes256GcmEncrypted` uses AES-256-GCM with a random 96-bit IV and authenticated additional data.
- `App\Support\SensitiveData::blindIndex()` stores HMAC-SHA256 indexes for lookup without decrypting values.
- API/model serialization masks sensitive fields by default through `MasksSensitiveAttributes`.
- Full reveal endpoints require a business `reason` and write immutable audit logs.

Production keys:

```env
PII_KEY_ID=pii-key-v1
PII_ENCRYPTION_KEY=base64:<32-byte-key>
PII_BLIND_INDEX_KEY=base64:<32-byte-or-longer-key>
```

Use AWS KMS, GCP KMS, Azure Key Vault, or HashiCorp Vault for production key custody and rotation. Do not store production keys in Git or shared `.env` files.

After deploying the schema migration, backfill existing plaintext rows:

```bash
php artisan security:encrypt-sensitive-data --dry-run
php artisan security:encrypt-sensitive-data
```

## Private Document Storage

Uploaded verification files are stored on `private_documents`, never on the public disk.

```env
SECURE_DOCUMENT_DISK=private_documents
SECURE_DOCUMENT_DRIVER=s3
AWS_PRIVATE_BUCKET=smartroom-private-documents
SECURE_DOCUMENT_URL_TTL=300
```

Recommended S3 controls:

- Block Public Access: on.
- ACLs disabled / bucket owner enforced.
- SSE-KMS enabled.
- Versioning enabled.
- Object Lock enabled for audit exports.
- Deny `aws:SecureTransport=false`.
- App IAM role scoped to the private bucket/prefix only.

Document review flow:

```text
Admin UI
  -> GET /admin/verification-documents/{id}
  -> RBAC + tenant check
  -> audit_logs append
  -> S3 presigned URL or local signed stream URL, 5 minutes
  -> private object
```

## RBAC And Masking

Default JSON responses mask PII:

```text
CCCD:          0791xxxx5678
Phone:         0901xxxx789
Bank account: 123xxxx5678
```

Reveal APIs:

- `POST /api/admin/residents/{resident}/sensitive`
- `POST /api/admin/tenant/bank-account/reveal`

Both require `reason` and write to `audit_logs`.

## Immutable Audit Logs

`audit_logs` is append-only:

- runtime app can insert/select, not update/delete in production grants.
- migration creates MySQL triggers that reject update/delete.
- each row stores `prev_hash` and `row_hash` to make tampering detectable.

Production hardening:

- Put database admin credentials outside the web app runtime.
- Stream logs to a separate security account/SIEM.
- Export daily audit batches to S3 Object Lock Compliance Mode.
- Sign daily digest with KMS asymmetric key.
- Alert on audit write failure or hash-chain verification failure.

```text
Application
  -> audit_logs insert
  -> DB append-only trigger
  -> SIEM / WORM archive
  -> daily signed digest
```
