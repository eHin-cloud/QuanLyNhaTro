# Admin Document Access Control

This workflow protects landlord onboarding documents such as CCCD, So Do, business registration, and fire-safety certificates.

Reference: Decree 13/2023/ND-CP requires personal data processing to respect the data subject's consent and personal-data protection measures. See the Government text: https://xaydungchinhsach.chinhphu.vn/toan-van-nghi-dinh-13-2023-nd-cp-bao-ve-du-lieu-ca-nhan-119230516104357809.htm

## Workflow

```text
Landlord uploads documents
  -> must check consent box
  -> landlord_verification_requests.admin_review_consent_given = true
  -> request status = pending

Platform Admin reviews pending request
  -> GET /admin/verification-documents/{document}
  -> backend checks consent + pending status
  -> admin_access_logs append
  -> 5-minute private URL

Admin approves/rejects
  -> request status = approved/rejected
  -> default_document_access_revoked_at = now()
  -> default read access stops

Post-approval document access
  -> POST /admin/verification-documents/{document}/unlock
  -> required reason
  -> backend validates admin role + prior consent + closed request
  -> admin_access_logs append
  -> 5-minute private URL
```

## API Endpoints

Web:

```text
GET  /admin/verification-documents/{document}
POST /admin/verification-documents/{document}/unlock
```

API:

```text
POST /api/platform-admin/verification-documents/{document}/unlock
Authorization: Bearer <sanctum-token>
Accept: application/json

{
  "reason": "Investigating deposit fraud report on Room #305"
}
```

Response:

```json
{
  "success": true,
  "url": "https://...",
  "expires_at": "2026-06-09T15:30:00+00:00"
}
```

## SQL Schema

Updated `landlord_verification_requests`:

```sql
ALTER TABLE landlord_verification_requests
  ADD COLUMN admin_review_consent_given TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN admin_review_consent_at TIMESTAMP NULL,
  ADD COLUMN admin_review_consent_ip VARCHAR(45) NULL,
  ADD COLUMN default_document_access_revoked_at TIMESTAMP NULL;
```

`admin_access_logs`:

```sql
CREATE TABLE admin_access_logs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  admin_user_id BIGINT UNSIGNED NULL,
  target_landlord_id BIGINT UNSIGNED NULL,
  tenant_id BIGINT UNSIGNED NULL,
  verification_request_id BIGINT UNSIGNED NULL,
  document_id BIGINT UNSIGNED NULL,
  document_type VARCHAR(80) NOT NULL,
  access_type VARCHAR(40) NOT NULL,
  reason TEXT NOT NULL,
  ip_address VARCHAR(45) NULL,
  user_agent TEXT NULL,
  presigned_url_expires_at TIMESTAMP NULL,
  prev_hash VARCHAR(64) NULL,
  row_hash VARCHAR(64) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX admin_access_logs_admin_created_idx (admin_user_id, created_at),
  INDEX admin_access_logs_landlord_created_idx (target_landlord_id, created_at),
  INDEX admin_access_logs_document_created_idx (document_id, created_at),
  INDEX admin_access_logs_tenant_created_idx (tenant_id, created_at),
  CONSTRAINT admin_access_logs_admin_user_id_foreign FOREIGN KEY (admin_user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT admin_access_logs_target_landlord_id_foreign FOREIGN KEY (target_landlord_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT admin_access_logs_tenant_id_foreign FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE SET NULL,
  CONSTRAINT admin_access_logs_verification_request_id_foreign FOREIGN KEY (verification_request_id) REFERENCES landlord_verification_requests(id) ON DELETE SET NULL,
  CONSTRAINT admin_access_logs_document_id_foreign FOREIGN KEY (document_id) REFERENCES landlord_verification_documents(id) ON DELETE SET NULL
);
```

Append-only triggers:

```sql
CREATE TRIGGER admin_access_logs_no_update
BEFORE UPDATE ON admin_access_logs
FOR EACH ROW
BEGIN
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'admin_access_logs is append-only';
END;

CREATE TRIGGER admin_access_logs_no_delete
BEFORE DELETE ON admin_access_logs
FOR EACH ROW
BEGIN
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'admin_access_logs is append-only';
END;
```

## DB Permission Model

Use separate database users:

```sql
CREATE USER 'smartroom_app'@'%' IDENTIFIED BY '<strong-password>';
CREATE USER 'smartroom_migrator'@'%' IDENTIFIED BY '<strong-password>';
CREATE USER 'smartroom_auditor'@'%' IDENTIFIED BY '<strong-password>';

GRANT SELECT, INSERT, UPDATE, DELETE ON smartroom.* TO 'smartroom_app'@'%';
REVOKE UPDATE, DELETE ON smartroom.admin_access_logs FROM 'smartroom_app'@'%';
GRANT INSERT, SELECT ON smartroom.admin_access_logs TO 'smartroom_app'@'%';

GRANT SELECT ON smartroom.admin_access_logs TO 'smartroom_auditor'@'%';

GRANT ALL PRIVILEGES ON smartroom.* TO 'smartroom_migrator'@'%';
```

Production notes:

- The app runtime must never use the migrator account.
- Only CI/CD migration jobs should know `smartroom_migrator`.
- Stream `admin_access_logs` to WORM storage or SIEM.
- Enable S3 Object Lock for audit exports.
- Rotate PII and blind-index keys through KMS/Vault.
