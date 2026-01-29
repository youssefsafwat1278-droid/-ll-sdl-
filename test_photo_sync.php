<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::find(153);
echo "User ID: " . $user->id . "\n";
echo "User scout_id: " . $user->scout_id . "\n";
echo "User photo_url: " . ($user->photo_url ?? 'NULL') . "\n\n";

$scout = \App\Models\Scout::where('scout_id', $user->scout_id)->first();
if ($scout) {
    echo "Scout found: YES\n";
    echo "Scout scout_id: " . $scout->scout_id . "\n";
    echo "Scout photo_url: " . ($scout->photo_url ?? 'NULL') . "\n";
} else {
    echo "Scout found: NO\n";
}
