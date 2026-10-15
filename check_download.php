<?php
/**
 * DIAGNOSTIC: Akses via browser: http://localhost/surat/check_download.php
 * Ini akan langsung generate dan download file DOCX tanpa Laravel
 */
chdir(__DIR__);
require 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;

// Tangkap semua output buffer yang mungkin sudah ada
$garbage = '';
while (ob_get_level() > 0) {
    $garbage .= ob_get_clean();
}

// Jika ada sampah, artinya output buffer memang sudah terkontaminasi
if (!empty(trim($garbage))) {
    // Tampilkan info debug alih-alih download
    header('Content-Type: text/plain');
    echo "ERROR: Output buffer sudah terkontaminasi sebelum download!\n";
    echo "Isi garbage: " . substr($garbage, 0, 500) . "\n";
    exit;
}

$phpWord = new PhpWord();
$phpWord->setDefaultFontName('Arial');
$phpWord->setDefaultFontSize(11);

$sectionStyle = [
    'paperSize'    => 'A4',
    'marginTop'    => Converter::cmToTwip(2.0),
    'marginBottom' => Converter::cmToTwip(2.0),
    'marginLeft'   => Converter::cmToTwip(2.5),
    'marginRight'  => Converter::cmToTwip(2.5),
];

$section = $phpWord->addSection($sectionStyle);
$section->addText('TES DOKUMEN WORD', ['bold' => true, 'size' => 14], ['alignment' => Jc::CENTER]);
$section->addText('Nomor: 001/TEST/X/2026', ['size' => 11], ['alignment' => Jc::CENTER]);
$section->addTextBreak(1);
$section->addText('Jika file ini terbuka di MS Word, maka download berfungsi dengan benar.', ['size' => 11], ['alignment' => Jc::BOTH]);
$section->addTextBreak(2);
$section->addText('Jakarta, ' . date('d F Y'), ['size' => 11], ['alignment' => Jc::RIGHT]);

// Simpan ke file storage
$dir = __DIR__ . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'word_temp';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}
$tmpFile = $dir . DIRECTORY_SEPARATOR . 'check_' . time() . '.docx';

$writer = IOFactory::createWriter($phpWord, 'Word2007');
$writer->save($tmpFile);

$filesize = filesize($tmpFile);
$filename = 'test_word_' . date('Ymd_His') . '.docx';

// Pastikan tidak ada output
if (headers_sent($file, $line)) {
    echo "GAGAL: Headers sudah dikirim di $file baris $line\n";
    echo "Filesize yang dihasilkan: $filesize bytes\n";
    unlink($tmpFile);
    exit;
}

// Kirim file
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . $filesize);
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Expires: 0');

readfile($tmpFile);
unlink($tmpFile);
exit;
