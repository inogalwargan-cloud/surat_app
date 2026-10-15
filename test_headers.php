<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\LetterTemplate;
use App\Services\LetterWordService;

$template = LetterTemplate::first();
$service = new LetterWordService();
$response = $service->generateWord($template);

foreach ($response->headers->all() as $name => $values) {
    echo $name . ": " . implode("; ", $values) . "\n";
}
