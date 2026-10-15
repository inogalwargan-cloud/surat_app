<?php

namespace App\Services;

use App\Enums\Config as ConfigEnum;
use App\Models\Config;
use App\Models\LetterTemplate;
use Carbon\Carbon;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\JcTable;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LetterWordService
{
    /**
     * Generate and download a Microsoft Word (.docx) document for the given letter template.
     *
     * @param LetterTemplate $template
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function generateWord(LetterTemplate $template): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $phpWord = new PhpWord();

        // Global font settings
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(11);

        // Define Section Styles (A4, 2.5cm left/right, 2cm top/bottom)
        $sectionStyle = [
            'paperSize'    => 'A4',
            'marginTop'    => Converter::cmToTwip(2.0),
            'marginBottom' => Converter::cmToTwip(2.0),
            'marginLeft'   => Converter::cmToTwip(2.5),
            'marginRight'  => Converter::cmToTwip(2.5),
        ];

        $section = $phpWord->addSection($sectionStyle);

        // 1. Add Official Letterhead (KOP SURAT)
        $this->addLetterhead($section);

        // 2. Render Letter Body based on Template Type
        if ($template->type === 'tugas') {
            $this->renderSuratTugas($section, $template);
        } else {
            $this->renderStandardLetter($section, $template);
        }

        // 3. Prepare safe file name
        $safeTitle = Str::slug($template->title ?: $template->type_label, '_');
        $fileName = ($safeTitle ?: 'template_surat') . '_' . date('Ymd_His') . '.docx';

        // 4. Simpan ke storage (hindari bug tempnam double-extension yang membuat file corrupt)
        $storageDir = storage_path('app' . DIRECTORY_SEPARATOR . 'word_temp');
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }
        $tempPath = $storageDir . DIRECTORY_SEPARATOR . uniqid('surat_', true) . '.docx';

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempPath);

        // 5. Stream download - bersihkan output buffer agar file binary tidak terkontaminasi
        return response()->streamDownload(function () use ($tempPath) {
            // Flush dan bersihkan semua output buffer aktif (dari WAMP/PHP session handler dll)
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            readfile($tempPath);
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }, $fileName, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Pragma'              => 'public',
            'Expires'             => '0',
        ]);
    }

    /**
     * Render Official Kop Surat
     */
    protected function addLetterhead(Section $section): void
    {
        $institutionName = 'INSTANSI / PERUSAHAAN';
        $institutionAddress = 'Alamat Instansi / Perusahaan';
        $institutionPhone = '-';
        $institutionEmail = '-';

        try {
            $institutionName = Config::getValueByCode(ConfigEnum::INSTITUTION_NAME) ?: $institutionName;
            $institutionAddress = Config::getValueByCode(ConfigEnum::INSTITUTION_ADDRESS) ?: $institutionAddress;
            $institutionPhone = Config::getValueByCode(ConfigEnum::INSTITUTION_PHONE) ?: $institutionPhone;
            $institutionEmail = Config::getValueByCode(ConfigEnum::INSTITUTION_EMAIL) ?: $institutionEmail;
        } catch (\Throwable $e) {
            // Keep defaults if config fails
        }

        $logoPath = public_path('logo-black.png');
        $hasLogo = file_exists($logoPath);

        $tableStyle = [
            'alignment' => JcTable::CENTER,
            'width' => 100 * 50,
            'cellMarginTop' => 40,
            'cellMarginBottom' => 40,
        ];
        $kopTable = $section->addTable($tableStyle);
        $kopTable->addRow();

        if ($hasLogo) {
            $logoCell = $kopTable->addCell(1600, ['valign' => 'center']);
            $logoCell->addImage($logoPath, [
                'width' => 55,
                'height' => 55,
                'alignment' => Jc::CENTER,
            ]);
            $textCell = $kopTable->addCell(7900, ['valign' => 'center']);
        } else {
            $textCell = $kopTable->addCell(9500, ['valign' => 'center']);
        }

        $textCell->addText(strtoupper($institutionName), [
            'bold' => true,
            'size' => 14,
            'color' => '111111',
        ], [
            'alignment' => Jc::CENTER,
            'spaceAfter' => 20,
        ]);

        $textCell->addText($institutionAddress, [
            'size' => 9.5,
            'color' => '333333',
        ], [
            'alignment' => Jc::CENTER,
            'spaceAfter' => 20,
        ]);

        $contactText = "Telepon: {$institutionPhone} | Email: {$institutionEmail}";
        $textCell->addText($contactText, [
            'size' => 9,
            'color' => '555555',
        ], [
            'alignment' => Jc::CENTER,
            'spaceAfter' => 40,
        ]);

        // Divider Line (Standard official kop bottom double/thick line)
        $dividerTable = $section->addTable(['alignment' => JcTable::CENTER, 'width' => 100 * 50]);
        $dividerTable->addRow();
        $dividerCell = $dividerTable->addCell(9500, [
            'borderBottomSize' => 18,
            'borderBottomColor' => '000000',
        ]);
        $dividerCell->addText('', [], ['spaceAfter' => 0]);

        $section->addTextBreak(1);
    }

    /**
     * Render Standard Letter format:
     * - Surat Permohonan
     * - Surat Pemberitahuan
     * - Surat Undangan
     * - Surat Balasan
     */
    protected function renderStandardLetter(Section $section, LetterTemplate $template): void
    {
        $specific = $template->specific_data ?? [];
        $letterPlace = $template->letter_place ?: 'Jakarta';
        $formattedDate = $template->letter_date
            ? Carbon::parse($template->letter_date)->isoFormat('D MMMM YYYY')
            : Carbon::now()->isoFormat('D MMMM YYYY');

        // Top Header Meta (Nomor, Lampiran, Perihal on left; Tempat, Tanggal on right)
        $metaTable = $section->addTable(['alignment' => JcTable::CENTER, 'width' => 100 * 50]);
        $metaTable->addRow();

        $leftCell = $metaTable->addCell(5700, ['valign' => 'top']);
        $rightCell = $metaTable->addCell(3800, ['valign' => 'top']);

        $refNo = $template->reference_number ?: '-';
        $attachment = $template->attachment ?: '-';
        $regarding = $template->regarding ?: $template->title;

        $leftCell->addText("Nomor     : {$refNo}", [], ['spaceAfter' => 20]);
        $leftCell->addText("Lampiran : {$attachment}", [], ['spaceAfter' => 20]);
        $leftCell->addText("Perihal    : {$regarding}", ['bold' => true], ['spaceAfter' => 40]);

        $rightCell->addText("{$letterPlace}, {$formattedDate}", [], [
            'alignment' => Jc::RIGHT,
            'spaceAfter' => 40,
        ]);

        $section->addTextBreak(1);

        // Kepada Yth. Recipient Block
        $toName = $template->to_name ?: 'Penerima Surat';
        $toAddress = $template->to_address ?: 'di Tempat';

        $section->addText("Kepada Yth.", ['bold' => false], ['spaceAfter' => 20]);
        $section->addText($toName, ['bold' => true], ['spaceAfter' => 20]);
        $this->addMultiLineText($section, $toAddress, false, 40);

        $section->addTextBreak(1);

        // Salam Pembuka
        $salutation = $template->opening_salutation ?: 'Dengan hormat,';
        $section->addText($salutation, [], ['spaceAfter' => 60]);

        // Paragraf Pembuka
        if ($template->opening_text) {
            $this->addMultiLineText($section, $template->opening_text, false, 80, true);
        }

        // Type-Specific structured sections
        if ($template->type === 'undangan') {
            $this->renderUndanganDetails($section, $specific);
        } elseif ($template->type === 'balasan') {
            $this->renderBalasanDetails($section, $specific);
        } elseif ($template->type === 'permohonan') {
            $this->renderPermohonanDetails($section, $specific);
        } elseif ($template->type === 'pemberitahuan') {
            $this->renderPemberitahuanDetails($section, $specific);
        }

        // Main Content / Isi Surat
        if ($template->content) {
            $this->addMultiLineText($section, $template->content, false, 80, true);
        }

        // Paragraf Penutup
        if ($template->closing_text) {
            $this->addMultiLineText($section, $template->closing_text, false, 80, true);
        }

        $section->addTextBreak(1);

        // Tanda Tangan (Signature Block)
        $this->renderSignatureBlock($section, $template);

        // Tembusan (if any)
        if ($template->copy_to) {
            $this->renderCopyTo($section, $template->copy_to);
        }
    }

    /**
     * Render Surat Tugas format
     */
    protected function renderSuratTugas(Section $section, LetterTemplate $template): void
    {
        $specific = $template->specific_data ?? [];
        $refNo = $template->reference_number ?: '-';

        // Title: SURAT TUGAS
        $section->addText("SURAT TUGAS", [
            'bold' => true,
            'size' => 14,
            'underline' => 'single',
        ], [
            'alignment' => Jc::CENTER,
            'spaceAfter' => 20,
        ]);

        $section->addText("Nomor: {$refNo}", [
            'size' => 11,
        ], [
            'alignment' => Jc::CENTER,
            'spaceAfter' => 120,
        ]);

        $section->addTextBreak(1);

        // Dasar Tugas (if provided)
        $basis = $specific['basis'] ?? null;
        if ($basis) {
            $dasarTable = $section->addTable(['alignment' => JcTable::CENTER, 'width' => 100 * 50]);
            $dasarTable->addRow();
            $dasarTable->addCell(1500, ['valign' => 'top'])->addText("Dasar :", ['bold' => true]);
            $cellContent = $dasarTable->addCell(8000, ['valign' => 'top']);
            $this->addMultiLineText($cellContent, $basis, false, 40);
            $section->addTextBreak(1);
        }

        // MEMERINTAHKAN:
        $section->addText("MEMERINTAHKAN:", [
            'bold' => true,
            'size' => 11,
        ], [
            'alignment' => Jc::CENTER,
            'spaceAfter' => 100,
        ]);

        // KEPADA:
        $assignedName = $specific['assigned_name'] ?? ($template->to_name ?: '-');
        $assignedNip = $specific['assigned_nip'] ?? '-';
        $assignedPosition = $specific['assigned_position'] ?? ($template->to_address ?: '-');
        $assignedRank = $specific['assigned_rank'] ?? '-';

        $kepadaTable = $section->addTable(['alignment' => JcTable::CENTER, 'width' => 100 * 50]);
        $kepadaTable->addRow();
        $kepadaTable->addCell(1500, ['valign' => 'top'])->addText("Kepada :", ['bold' => true]);

        $detailCell = $kepadaTable->addCell(8000, ['valign' => 'top']);
        $tablePerson = $detailCell->addTable(['width' => 100 * 50]);

        $this->addKeyValueRow($tablePerson, "Nama", $assignedName, true);
        if ($assignedNip && $assignedNip !== '-') {
            $this->addKeyValueRow($tablePerson, "NIP / Identitas", $assignedNip);
        }
        if ($assignedRank && $assignedRank !== '-') {
            $this->addKeyValueRow($tablePerson, "Pangkat / Gol.", $assignedRank);
        }
        $this->addKeyValueRow($tablePerson, "Jabatan", $assignedPosition);

        $section->addTextBreak(1);

        // UNTUK:
        $taskTable = $section->addTable(['alignment' => JcTable::CENTER, 'width' => 100 * 50]);
        $taskTable->addRow();
        $taskTable->addCell(1500, ['valign' => 'top'])->addText("Untuk :", ['bold' => true]);

        $taskCell = $taskTable->addCell(8000, ['valign' => 'top']);
        if ($template->content) {
            $this->addMultiLineText($taskCell, $template->content, false, 40);
        }

        // Additional task specifics: Date & Location
        $taskDate = $specific['task_date'] ?? null;
        $taskLocation = $specific['task_location'] ?? null;

        if ($taskDate || $taskLocation) {
            $subTable = $taskCell->addTable(['width' => 100 * 50]);
            if ($taskDate) {
                $this->addKeyValueRow($subTable, "Waktu", $taskDate);
            }
            if ($taskLocation) {
                $this->addKeyValueRow($subTable, "Tempat", $taskLocation);
            }
        }

        $section->addTextBreak(1);

        // Paragraf Penutup
        $closing = $template->closing_text ?: 'Demikian surat tugas ini dibuat untuk dilaksanakan dengan sebaik-baiknya dan penuh rasa tanggung jawab.';
        $this->addMultiLineText($section, $closing, false, 80, true);

        $section->addTextBreak(1);

        // Tanda Tangan
        $this->renderSignatureBlock($section, $template);

        // Tembusan
        if ($template->copy_to) {
            $this->renderCopyTo($section, $template->copy_to);
        }
    }

    /**
     * Render Specific Details for Surat Undangan
     */
    protected function renderUndanganDetails(Section $section, array $specific): void
    {
        $date = $specific['event_date'] ?? null;
        $time = $specific['event_time'] ?? null;
        $location = $specific['event_location'] ?? null;
        $agenda = $specific['event_agenda'] ?? null;

        if ($date || $time || $location || $agenda) {
            $table = $section->addTable(['alignment' => JcTable::CENTER, 'width' => 90 * 50]);
            if ($date) {
                $this->addKeyValueRow($table, "Hari, Tanggal", $date);
            }
            if ($time) {
                $this->addKeyValueRow($table, "Waktu", $time);
            }
            if ($location) {
                $this->addKeyValueRow($table, "Tempat", $location);
            }
            if ($agenda) {
                $this->addKeyValueRow($table, "Acara / Agenda", $agenda);
            }
            $section->addTextBreak(1);
        }
    }

    /**
     * Render Specific Details for Surat Balasan
     */
    protected function renderBalasanDetails(Section $section, array $specific): void
    {
        $refNo = $specific['ref_letter_number'] ?? null;
        $refDate = $specific['ref_letter_date'] ?? null;
        $refSubject = $specific['ref_letter_subject'] ?? null;

        if ($refNo || $refDate || $refSubject) {
            $table = $section->addTable(['alignment' => JcTable::CENTER, 'width' => 90 * 50]);
            if ($refNo) {
                $this->addKeyValueRow($table, "Merujuk Surat No.", $refNo);
            }
            if ($refDate) {
                $this->addKeyValueRow($table, "Tanggal Surat", $refDate);
            }
            if ($refSubject) {
                $this->addKeyValueRow($table, "Perihal Surat", $refSubject);
            }
            $section->addTextBreak(1);
        }
    }

    /**
     * Render Specific Details for Surat Permohonan
     */
    protected function renderPermohonanDetails(Section $section, array $specific): void
    {
        $item = $specific['request_item'] ?? null;
        $reason = $specific['request_reason'] ?? null;

        if ($item || $reason) {
            $table = $section->addTable(['alignment' => JcTable::CENTER, 'width' => 90 * 50]);
            if ($item) {
                $this->addKeyValueRow($table, "Permohonan", $item);
            }
            if ($reason) {
                $this->addKeyValueRow($table, "Keperluan", $reason);
            }
            $section->addTextBreak(1);
        }
    }

    /**
     * Render Specific Details for Surat Pemberitahuan
     */
    protected function renderPemberitahuanDetails(Section $section, array $specific): void
    {
        $effectiveDate = $specific['effective_date'] ?? null;
        $targetAudience = $specific['target_audience'] ?? null;

        if ($effectiveDate || $targetAudience) {
            $table = $section->addTable(['alignment' => JcTable::CENTER, 'width' => 90 * 50]);
            if ($targetAudience) {
                $this->addKeyValueRow($table, "Ditujukan Kepada", $targetAudience);
            }
            if ($effectiveDate) {
                $this->addKeyValueRow($table, "Mulai Berlaku", $effectiveDate);
            }
            $section->addTextBreak(1);
        }
    }

    /**
     * Render Signer Signature Block (Right Aligned)
     */
    protected function renderSignatureBlock(Section $section, LetterTemplate $template): void
    {
        $letterPlace = $template->letter_place ?: 'Jakarta';
        $formattedDate = $template->letter_date
            ? Carbon::parse($template->letter_date)->isoFormat('D MMMM YYYY')
            : Carbon::now()->isoFormat('D MMMM YYYY');

        $signerPosition = $template->signer_position ?: 'Pimpinan';
        $signerName = $template->signer_name ?: 'Nama Penandatangan';
        $signerNip = $template->signer_identity_number ?: null;

        $sigTable = $section->addTable(['alignment' => JcTable::CENTER, 'width' => 100 * 50]);
        $sigTable->addRow();

        // Left spacer column (60%)
        $sigTable->addCell(5200, ['valign' => 'top']);

        // Right signature column (40%)
        $rightCol = $sigTable->addCell(4300, ['valign' => 'top']);

        $rightCol->addText("{$letterPlace}, {$formattedDate}", [], ['spaceAfter' => 20]);
        $rightCol->addText($signerPosition, ['bold' => true], ['spaceAfter' => 20]);

        // Space for manual or digital signature
        $rightCol->addTextBreak(3);

        $rightCol->addText($signerName, [
            'bold' => true,
            'underline' => 'single',
        ], ['spaceAfter' => 20]);

        if ($signerNip) {
            $rightCol->addText("NIP. {$signerNip}", [], ['spaceAfter' => 20]);
        }
    }

    /**
     * Render Tembusan (Carbon Copy)
     */
    protected function renderCopyTo(Section $section, string $copyTo): void
    {
        $section->addTextBreak(1);
        $section->addText("Tembusan:", ['bold' => true], ['spaceAfter' => 20]);
        $this->addMultiLineText($section, $copyTo, false, 20);
    }

    /**
     * Helper to add Key-Value row in Table
     */
    protected function addKeyValueRow($table, string $key, string $value, bool $boldValue = false): void
    {
        $table->addRow();
        $table->addCell(2500, ['valign' => 'top'])->addText($key, ['bold' => false], ['spaceAfter' => 20]);
        $table->addCell(400, ['valign' => 'top'])->addText(":", [], ['spaceAfter' => 20]);
        $table->addCell(5500, ['valign' => 'top'])->addText($value, ['bold' => $boldValue], ['spaceAfter' => 20]);
    }

    /**
     * Helper to cleanly split text by newline and add to section/cell
     */
    protected function addMultiLineText($target, string $text, bool $bold = false, int $spaceAfter = 40, bool $justify = false): void
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($text));
        $paragraphStyle = [
            'spaceAfter' => $spaceAfter,
        ];
        if ($justify) {
            $paragraphStyle['alignment'] = Jc::BOTH;
        }

        foreach ($lines as $line) {
            if (trim($line) === '') {
                $target->addTextBreak(1);
            } else {
                $target->addText($line, ['bold' => $bold], $paragraphStyle);
            }
        }
    }
}
