<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::find(153);
echo "User ID: " . $user->id . "\n";
echo "User scout_id: " . $user->scout_id . "\n\n";

$scout = \App\Models\Scout::where('scout_id', $user->scout_id)->first();

if ($scout) {
    echo "Scout found: YES\n";
    echo "Scout scout_id: " . $scout->scout_id . "\n";
    echo "Scout photo_url BEFORE: " . ($scout->photo_url ?? 'NULL') . "\n\n";

    // Try to update
    echo "Attempting to update scout photo_url...\n";
    $newPhotoUrl = '/images/users/user_153_1768606520.png';

    $scout->photo_url = $newPhotoUrl;
    echo "Set photo_url to: " . $scout->photo_url . "\n";

    $result = $scout->save();
    echo "Save result: " . ($result ? 'TRUE' : 'FALSE') . "\n";
    echo "Scout is dirty: " . ($scout->isDirty() ? 'YES' : 'NO') . "\n\n";

    // Refresh from database
    $scout->refresh();
    echo "Scout photo_url AFTER refresh: " . ($scout->photo_url ?? 'NULL') . "\n";

    // Try alternative update method
    echo "\nTrying update() method instead...\n";
    $result2 = \App\Models\Scout::where('scout_id', $user->scout_id)->update(['photo_url' => $newPhotoUrl]);
    echo "Update result: " . $result2 . " rows affected\n";

    // Check again
    $scout->refresh();
    echo "Scout photo_url FINAL: " . ($scout->photo_url ?? 'NULL') . "\n";
} else {
    echo "Scout found: NO\n";
}
