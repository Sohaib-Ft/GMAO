<?php
require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Foundation\Application;

$app = new Application(realpath(__DIR__ . '/../'));
$app->useAppPath(realpath(__DIR__ . '/../app'));

// Bootstrap minimal
$bootstrap = require $app->basePath() . '/bootstrap/app.php';
$app = $bootstrap;
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Bootstrapped; run simple DB query
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$db = $app->make(Illuminate\Database\DatabaseManager::class);

$rows = $db->table('sessions')->orderBy('last_activity', 'desc')->limit(20)->get();

foreach ($rows as $r) {
    echo "ID: {$r->id}\n";
    echo "IP: {$r->ip_address}\n";
    echo "User Agent: {$r->user_agent}\n";
    echo "Last activity: " . date('Y-m-d H:i:s', $r->last_activity) . "\n";
    echo "Payload (truncated): " . substr($r->payload, 0, 200) . "\n";
    // Try to decode payload (Laravel stores serialized base64 payload)
    $decoded = @base64_decode($r->payload);
    if ($decoded !== false) {
        $un = @unserialize($decoded);
        if ($un !== false && is_array($un)) {
            echo "Decoded keys: " . implode(', ', array_keys($un)) . "\n";
            // If auth user id present, print it
            foreach ($un as $k => $v) {
                if (strpos($k, 'login_') === 0 || strpos($k, 'auth_') === 0 || strpos($k, 'user') !== false) {
                    echo "  $k => ";
                    var_export($v);
                    echo "\n";
                }
            }
        }
    }
    echo "---\n";
}

echo "Total sessions: " . $db->table('sessions')->count() . "\n";
