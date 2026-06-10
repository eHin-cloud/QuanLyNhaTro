<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::all();
foreach ($users as $user) {
    if (!$user->tenant_id) {
        echo "User: {$user->username} (Platform Admin) -> No tenant\n";
        continue;
    }
    $tenant = App\Models\Tenant::find($user->tenant_id);
    echo "User: {$user->username} -> Tenant: {$tenant->name}\n";
    foreach ($tenant->buildings as $b) {
        echo "  - Building: {$b->name} | Address: {$b->address}\n";
    }
}
