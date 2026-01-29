<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::find(153);
$scout = \App\Models\Scout::where('scout_id', 'SC012')->first();

echo "=== CURRENT STATE ===\n\n";
echo "User ID: " . $user->id . "\n";
echo "User scout_id: " . $user->scout_id . "\n";
echo "User photo_url: " . ($user->photo_url ?? 'NULL') . "\n\n";

echo "Scout scout_id: " . $scout->scout_id . "\n";
echo "Scout photo_url: " . ($scout->photo_url ?? 'NULL') . "\n\n";

echo "Are they matching? " . ($user->photo_url === $scout->photo_url ? 'YES ✓' : 'NO ✗') . "\n";
