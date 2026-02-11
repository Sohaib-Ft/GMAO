<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Mail;

try {
    Mail::raw('Test email from CMMS', function ($message) {
        $message->to('test@example.com')->subject('CMMS Test Mail');
    });
    echo "Mail send attempted. Check MailHog or mail logs.\n";
} catch (\Throwable $e) {
    echo "Mail send failed: " . $e->getMessage() . "\n";
}
