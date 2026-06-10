# AI Memory

## Latest Request
- Implemented Step 3 of the progressive verification flow: Premium Trust Badge / Verified Status.
- Landlords with KYC verified can submit optional "Tich xanh" documents from `/smartroom/admin`.
- Platform admin can review KYC and Premium verification requests from `/admin/verifications`, approve/reject them, and trigger the correct tenant status/badge/boost transitions.

## Proposed Database Logic
- Add landlord onboarding/verification status fields to tenants/users:
  - `unverified_landlord`
  - `kyc_pending`
  - `kyc_verified`
  - `premium_pending`
  - `premium_verified`
  - `rejected`
- Add verification request tables for KYC, payout/bank verification, and premium compliance documents.
- Public listings should show an `Unverified` badge for unverified landlords and a `Verified` badge/boost for premium verified landlords.

## Files Created/Changed
- Created `app/Http/Controllers/LandlordVerificationController.php`
  - POST `/smartroom/admin/verification/kyc`
  - POST `/smartroom/admin/verification/premium`
  - Validates CCCD/bank fields and required proof images.
  - Supersedes previous pending KYC requests, creates a new pending verification request, stores uploaded files on the public disk, and updates tenant to `kyc_pending`.
  - Validates and stores premium documents: DKKD, PCCC, and ANTT certificates; updates tenant to `premium_pending`.
- Created `app/Http/Controllers/AdminVerificationController.php`
  - GET `/admin/verifications`
  - POST `/admin/verifications/{verification}/approve`
  - POST `/admin/verifications/{verification}/reject`
  - Approving KYC sets tenant to `kyc_verified`, listing badge to `kyc_verified`, and promotes `unverified_landlord` users to `landlord`.
  - Approving Premium sets tenant to `premium_verified`, listing badge to `premium_verified`, and raises `boost_score` to at least 100.
  - Rejecting KYC marks tenant `rejected`; rejecting Premium returns tenant to `kyc_verified`.
- Created `app/Models/LandlordVerificationRequest.php`.
- Created `app/Models/LandlordVerificationDocument.php`.
- Updated `app/Http/Controllers/AdminDashboardController.php`
  - Loads latest KYC and Premium requests for dashboard display.
  - Blocks automatic payment reminders with bank/VietQR info until tenant is KYC-or-better verified.
  - Skips payment reminders in "notify all" when KYC is not verified, while still allowing contract/maintenance reminders.
- Updated `app/Http/Controllers/PaymentController.php`
  - Blocks marking a bill as paid via `bank_transfer` or `vietqr` until KYC-or-better verification is complete.
- Updated `app/Http/Controllers/ResidentPortalController.php`
  - Blocks resident QR payment page until landlord KYC-or-better verification is complete.
- Updated `resources/views/admin/admin.blade.php`
  - Adds KYC upload form/status card.
  - Adds Premium/Tich xanh upload form/status card for `kyc_verified` landlords.
  - Shows `premium_pending` and `premium_verified` dashboard states.
  - Shows verified payout success state.
  - Blocks client-side VietQR modal/fallback before KYC.
- Created `resources/views/admin/verifications/index.blade.php`
  - Platform admin review page for KYC and Premium verification requests with document links, approve action, and reject reason form.
- Updated `resources/views/login/login.blade.php`
  - Adds admin link to verification review page from user management.
- Updated `routes/web.php`
  - Adds KYC submission route, Premium submission route, and platform admin verification review/approve/reject routes.
- Updated `routes/api.php`
  - Adds `GET /api/utility-bill/{id}/qr` with tenant scoping and KYC guard.
  - Hardens `/api/utility-bills/auto-remind` with tenant scoping, KYC guard, and tenant bank details instead of hardcoded sample bank data.
  - Allows online payment flows for `kyc_verified`, `premium_pending`, and `premium_verified`.
- Created `app/Http/Controllers/LandlordOnboardingController.php`
  - GET `/landlord/register`
  - POST `/landlord/register`
  - Validates phone, OTP demo (`123456`/`000000`), name, property name/address, password.
  - Creates Tenant, User, LandlordProfile, and initial Building in a transaction.
- Created `app/Models/LandlordProfile.php`.
- Created `resources/views/landlord/onboarding.blade.php`.
- Created `database/migrations/2026_06_09_000003_landlord_progressive_verification.php`
  - Adds `unverified_landlord` role.
  - Adds tenant verification fields.
  - Adds landlord profile, verification request, and verification document tables.
- Updated role helpers in `app/Models/User.php`.
- Updated `app/Http/Middleware/AdminMiddleware.php`, `app/Http/Middleware/CheckRole.php`, `RoomController`, and `ResidentPortalController` so `unverified_landlord` can access landlord dashboard/room setup while landlord-only sensitive actions stay protected.
- Updated `routes/web.php` with onboarding routes and Renty trust badge mapping/boost sort.
- Updated `resources/views/rentry/rentry.blade.php` and `resources/views/rentry/rooms/show.blade.php` to show trust badges.
- Updated `resources/views/admin/admin.blade.php` to show a non-punitive verification progress card for unverified landlords.
- Updated `resources/views/login/login.blade.php` to link to landlord onboarding.

## Proposed Backend Flow
- Step 1: Low-friction landlord registration creates user, tenant, landlord profile, and grants dashboard access immediately.
- Step 2: Trigger KYC/payout verification only when financial actions happen. Current implementation accepts KYC submission and locks online payment/VietQR flows until admin approval sets tenant to `kyc_verified`.
- Step 3: Optional premium verification collects business registration, fire safety, and security/order certificates. Admin approval sets `premium_verified`, `listing_badge = premium_verified`, and boosts listings.

## TODO
- Consider moving API QR/auto-remind closures from `routes/api.php` into a dedicated controller for maintainability.
- Add a more polished admin verification detail page with document previews and audit trail filters.
- Add notification/email to landlord after KYC/Premium approval or rejection.
- Replace demo OTP with real SMS/Zalo OTP provider.

## Verification Run
- `php -l` passed for new/changed PHP files.
- `php artisan migrate` ran `2026_06_09_000003_landlord_progressive_verification`.
- Note: an existing pending migration `2026_06_09_000004_create_room_reports_table` also ran during the same migrate command; it was not created in this onboarding task.
- `php artisan view:cache` passed.
- HTTP GET `/landlord/register` returned 200.
- Test POST registration created a new landlord and redirected to `/smartroom/admin`.
- New landlord could open `/smartroom/admin/rooms/create` and see the auto-created building.
- HTTP GET `/renty` returned 200.
- Step 2 verification:
  - `php -l` passed for `LandlordVerificationController`, `LandlordVerificationRequest`, `LandlordVerificationDocument`, `AdminDashboardController`, `PaymentController`, `ResidentPortalController`, `routes/web.php`, and `routes/api.php`.
  - `php artisan route:list --path=smartroom/admin/verification/kyc` shows the KYC POST route.
  - `php artisan route:list --path=utility-bill` shows `GET api/utility-bill/{id}/qr` and `POST api/utility-bills/auto-remind`.
  - `php artisan view:cache` passed after dashboard KYC UI changes.
  - `git diff --check` passed.
  - HTTP GET `/smartroom/admin` returned expected 302 to login when unauthenticated, confirming local server response without a pre-auth crash.
  - Browser plugin verification was attempted but blocked by the local Node/browser sandbox error `windows sandbox failed: spawn setup refresh`; fallback verification used artisan/HTTP commands.
- Step 3 verification:
  - `php -l` passed for `AdminVerificationController`, `LandlordVerificationController`, `AdminDashboardController`, `PaymentController`, `ResidentPortalController`, `routes/web.php`, and `routes/api.php`.
  - `php artisan route:list --path=smartroom/admin/verification` shows KYC and Premium landlord submission routes.
  - `php artisan route:list --path=admin/verifications` shows admin index/approve/reject routes.
  - `php artisan view:cache` passed after adding dashboard Premium UI and admin review page.
  - `git diff --check` passed.
  - HTTP GET `/admin/verifications` returned expected 302 to login when unauthenticated.
