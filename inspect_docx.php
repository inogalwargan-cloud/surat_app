<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\LetterTemplate;
use App\Services\LetterWordService;
use PhpOffice\PhpWord\Settings;

Settings::setOutputEscapingEnabled(true);
$service = new LetterWordService();
$types = ['permohonan', 'pemberitahuan', 'undangan', 'balasan', 'tugas'];

foreach ($types as $type) {
    $template = LetterTemplate::where('type', $type)->first();
    if (!$template) {
        echo "Template $type not found!\n";
        continue;
    }
    $response = $service->generateWord($template);
    $path = $response->getFile()->getPathname();

    $zip = new ZipArchive();
    if ($zip->open($path) === true) {
        $xml = $zip->getFromName('word/document.xml');
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $errors = libxml_get_errors();
        if (empty($errors)) {
            echo "Type [{$type}]: XML is 100% VALID & WELL-FORMED!\n";
        } else {
            echo "Type [{$type}]: XML ERRORS FOUND:\n";
            foreach ($errors as $error) {
                echo " - Line {$error->line}: {$error->message}\n";
            }
        }
        libxml_clear_errors();
        $zip->close();
    } else {
        echo "Type [{$type}]: Failed to open zip!\n";
    }
}
