<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
// Get kernel from container
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
echo "Kernel class: " . get_class($kernel) . "\n\n";
 $ref = new ReflectionClass($kernel);
foreach (['middlewareAliases','routeMiddleware'] as $name) {
    if ($ref->hasProperty($name)) {
        $p = $ref->getProperty($name);
        $p->setAccessible(true);
        echo "$name:\n";
        print_r($p->getValue($kernel));
        echo "\n";
    } else {
        echo "$name not found\n\n";
    }
}
