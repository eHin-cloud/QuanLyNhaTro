<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$buildings = \App\Models\Building::all();
foreach ($buildings as $b) {
    echo "ID: " . $b->id . " | Name: " . $b->name . " | Address: " . $b->address . "\n";
}
