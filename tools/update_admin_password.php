<?php
chdir(__DIR__ . '/..');
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
// Bootstrap the application
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = env('ADMIN_UPDATE_EMAIL');
$password = env('ADMIN_UPDATE_PASSWORD');

if (!$email || !$password) {
    die("Error: ADMIN_UPDATE_EMAIL or ADMIN_UPDATE_PASSWORD not set in .env\n");
}

$affected = User::where('email', $email)->update([
    'password' => Hash::make($password),
]);

if ($affected) {
    echo "Password updated for $email (rows: $affected)\n";
} else {
    echo "No user found with email $email\n";
}
