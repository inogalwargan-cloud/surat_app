<?php

$zip = new ZipArchive();
if ($zip->open('C:\Users\inoje\AppData\Local\Temp\sur50D6.tmp.docx') === true) {
    echo $zip->getFromName('word/_rels/document.xml.rels');
    $zip->close();
} else {
    // try finding the generated docx from inspect_docx
    require 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    $t = \App\Models\LetterTemplate::first();
    $s = new \App\Services\LetterWordService();
    $resp = $s->generateWord($t);
    $path = $resp->getFile()->getPathname();
    $zip->open($path);
    echo $zip->getFromName('word/_rels/document.xml.rels');
    $zip->close();
}
