<?php

namespace App\Enums;

enum TemplateType: string
{
    case PERMOHONAN = 'permohonan';
    case PEMBERITAHUAN = 'pemberitahuan';
    case UNDANGAN = 'undangan';
    case BALASAN = 'balasan';
    case TUGAS = 'tugas';

    public function label(): string
    {
        return match ($this) {
            self::PERMOHONAN => 'Surat Permohonan',
            self::PEMBERITAHUAN => 'Surat Pemberitahuan',
            self::UNDANGAN => 'Surat Undangan',
            self::BALASAN => 'Surat Balasan',
            self::TUGAS => 'Surat Tugas',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::PERMOHONAN => 'primary',
            self::PEMBERITAHUAN => 'warning',
            self::UNDANGAN => 'success',
            self::BALASAN => 'info',
            self::TUGAS => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PERMOHONAN => 'bx bx-send',
            self::PEMBERITAHUAN => 'bx bx-bell',
            self::UNDANGAN => 'bx bx-calendar-event',
            self::BALASAN => 'bx bx-reply',
            self::TUGAS => 'bx bx-briefcase-alt-2',
        };
    }

    public static function casesArray(): array
    {
        return [
            self::PERMOHONAN->value => self::PERMOHONAN->label(),
            self::PEMBERITAHUAN->value => self::PEMBERITAHUAN->label(),
            self::UNDANGAN->value => self::UNDANGAN->label(),
            self::BALASAN->value => self::BALASAN->label(),
            self::TUGAS->value => self::TUGAS->label(),
        ];
    }
}
