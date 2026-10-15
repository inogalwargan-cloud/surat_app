<?php

namespace Database\Seeders;

use App\Models\LetterTemplate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LetterTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $userId = User::first()?->id ?? 1;
        $today = Carbon::today()->format('Y-m-d');

        $templates = [
            [
                'title' => 'Template Surat Permohonan Izin Tempat dan Sarana',
                'type' => 'permohonan',
                'reference_number' => '001/SP/X/2026',
                'regarding' => 'Permohonan Izin Penggunaan Gedung dan Sarana',
                'attachment' => '1 (satu) Berkas Proposal',
                'letter_date' => $today,
                'letter_place' => 'Jakarta',
                'to_name' => 'Kepala Dinas Pemuda dan Olahraga',
                'to_address' => "Jalan Pemuda Raya No. 45\ndi Tempat",
                'opening_salutation' => 'Dengan hormat,',
                'opening_text' => 'Sehubungan dengan rencana pelaksanaan kegiatan Pelatihan Kepemimpinan dan Pengembangan Kapasitas Aparatur Tahun 2026, bersama surat ini kami bermaksud mengajukan permohonan izin penggunaan Gedung Serbaguna beserta fasilitas pendukungnya.',
                'content' => "Adapun rincian pelaksanaan kegiatan tersebut adalah sebagai berikut:\n• Hari / Tanggal : Senin s/d Rabu, 20 - 22 Oktober 2026\n• Waktu : Pukul 08.00 WIB s/d 16.00 WIB\n• Jumlah Peserta : ± 60 (enam puluh) Orang\n\nBesar harapan kami kiranya Bapak/Ibu berkenan mengabulkan permohonan ini demi kelancaran dan kesuksesan kegiatan tersebut.",
                'closing_text' => 'Demikian surat permohonan ini kami sampaikan. Atas perhatian, perkenan, dan kerja sama yang baik, kami ucapkan terima kasih.',
                'closing_salutation' => 'Hormat kami,',
                'signer_position' => 'Ketua Panitia Pelaksana',
                'signer_name' => 'Dr. H. Bambang Suryono, M.Si',
                'signer_identity_number' => '19750812 199903 1 004',
                'copy_to' => "1. Sekretaris Daerah\n2. Arsip Bagian Umum",
                'specific_data' => [
                    'request_item' => 'Izin Penggunaan Gedung Serbaguna & Sound System',
                    'request_reason' => 'Pelatihan Kepemimpinan dan Pengembangan Kapasitas',
                ],
                'user_id' => $userId,
            ],
            [
                'title' => 'Template Surat Pemberitahuan Penyesuaian Jadwal Layanan',
                'type' => 'pemberitahuan',
                'reference_number' => '002/PBT/X/2026',
                'regarding' => 'Pemberitahuan Penyesuaian Jam Operasional Layanan',
                'attachment' => '-',
                'letter_date' => $today,
                'letter_place' => 'Jakarta',
                'to_name' => 'Seluruh Pimpinan Unit Kerja, Karyawan, dan Pengguna Layanan',
                'to_address' => "di Lingkungan Kantor\ndi Tempat",
                'opening_salutation' => 'Dengan hormat,',
                'opening_text' => 'Menindaklanjuti arahan pimpinan mengenai peningkatan mutu infrastruktur pusat data dan pemeliharaan server berkala, bersama ini kami sampaikan pemberitahuan penting terkait pelayanan administrasi:',
                'content' => "1. Pada hari Jumat, 24 Oktober 2026, layanan administrasi perkantoran tatap muka akan beroperasi hingga pukul 14.00 WIB.\n2. Proses pemeliharaan sistem server (maintenance) akan berlangsung mulai hari Jumat pukul 15.00 WIB hingga Sabtu pukul 18.00 WIB.\n3. Seluruh aktivitas pelayanan persuratan dan administrasi akan kembali beroperasi normal pada hari Senin, 27 Oktober 2026 mulai pukul 08.00 WIB.\n\nDemikian pemberitahuan ini disampaikan agar dapat dipedomani dan menjadi perhatian bagi seluruh jajaran terkait.",
                'closing_text' => 'Atas perhatian, pengertian, dan kerja sama Saudara, kami ucapkan terima kasih.',
                'closing_salutation' => 'Hormat kami,',
                'signer_position' => 'Kepala Bagian Umum dan Tata Usaha',
                'signer_name' => 'M. Iqbal Effendi, S.T., M.Kom',
                'signer_identity_number' => '19820519 200801 1 007',
                'copy_to' => "1. Direktur Utama\n2. Para Kepala Divisi\n3. Pertinggal",
                'specific_data' => [
                    'effective_date' => '24 - 27 Oktober 2026',
                    'target_audience' => 'Seluruh Pegawai dan Pengguna Layanan',
                ],
                'user_id' => $userId,
            ],
            [
                'title' => 'Template Surat Undangan Rapat Koordinasi dan Evaluasi Kinerja',
                'type' => 'undangan',
                'reference_number' => '003/UND/X/2026',
                'regarding' => 'Undangan Rapat Koordinasi Capaian Kinerja Triwulan',
                'attachment' => '1 (satu) Berkas Lembar Agenda Rapat',
                'letter_date' => $today,
                'letter_place' => 'Jakarta',
                'to_name' => 'Bapak/Ibu Kepala Bagian dan Koordinator Tim',
                'to_address' => 'di Tempat',
                'opening_salutation' => 'Dengan hormat,',
                'opening_text' => 'Puji dan syukur senantiasa kita panjatkan ke hadirat Tuhan Yang Maha Kuasa. Sehubungan dengan penutupan triwulan dan evaluasi target program kerja berjalan, kami mengundang Bapak/Ibu untuk menghadiri rapat koordinasi yang akan dilaksanakan pada:',
                'content' => "Mengingat pentingnya agenda tersebut untuk menyelaraskan target kinerja instansi, diharapkan Bapak/Ibu hadir tepat pada waktunya dan membawa laporan capaian program kerja masing-masing unit.\n\nBagi pejabat yang berhalangan hadir, wajib menunjuk perwakilan resmi setingkat dengan surat mandat tertulis.",
                'closing_text' => 'Demikian surat undangan ini kami sampaikan. Atas perhatian dan kehadiran Bapak/Ibu, kami ucapkan terima kasih.',
                'closing_salutation' => 'Hormat kami,',
                'signer_position' => 'Sekretaris Instansi',
                'signer_name' => 'Dra. Hj. Nurul Hidayati, M.M.',
                'signer_identity_number' => '19690314 199403 2 001',
                'copy_to' => "1. Pimpinan Instansi (sebagai laporan)\n2. Arsip",
                'specific_data' => [
                    'event_date' => 'Rabu, 22 Oktober 2026',
                    'event_time' => 'Pukul 09.00 WIB s/d 12.30 WIB',
                    'event_location' => 'Ruang Rapat Utama Lantai 2',
                    'event_agenda' => "1. Evaluasi Capaian Kinerja Triwulan Berjalan\n2. Pembahasan Hambatan & Rencana Solusi Lapangan\n3. Finalisasi Target Program Kerja Lanjutan",
                ],
                'user_id' => $userId,
            ],
            [
                'title' => 'Template Surat Balasan Permohonan Kerja Praktik Mahasiswa',
                'type' => 'balasan',
                'reference_number' => '004/SB/X/2026',
                'regarding' => 'Persetujuan Permohonan Kerja Praktik / Magang',
                'attachment' => '-',
                'letter_date' => $today,
                'letter_place' => 'Jakarta',
                'to_name' => 'Dekan Fakultas Ilmu Komputer, Universitas Indonesia',
                'to_address' => "Jalan Salemba Raya No. 4\nJakarta Pusat",
                'opening_salutation' => 'Dengan hormat,',
                'opening_text' => 'Menindaklanjuti surat Saudara Nomor: 456/UN/FASILKOM/IX/2026 tertanggal 28 September 2026 perihal Permohonan Kerja Praktik / Magang Mahasiswa, bersama ini kami sampaikan tanggapan sebagai berikut:',
                'content' => "1. Pada prinsipnya pihak pimpinan menyambut baik dan MENYETUJUI permohonan magang untuk mahasiswa dari institusi Saudara di Divisi Sistem Informasi.\n2. Waktu pelaksanaan program magang disepakati terhitung mulai tanggal 1 November 2026 s/d 31 Januari 2027.\n3. Mahasiswa yang ditugaskan diwajibkan melapor ke Bagian Kepegawaian pada hari pertama dengan menyerahkan pas foto terbaru dan mematuhi tata tertib instansi.",
                'closing_text' => 'Demikian surat balasan ini kami sampaikan untuk menjadi perhatian dan dipergunakan sebagaimana mestinya. Atas kerja sama yang baik, kami ucapkan terima kasih.',
                'closing_salutation' => 'Hormat kami,',
                'signer_position' => 'Kepala Bagian Sumber Daya Manusia',
                'signer_name' => 'Hendro Prasetyo, S.H., M.H.',
                'signer_identity_number' => '19790625 200501 1 003',
                'copy_to' => "1. Kepala Divisi Sistem Informasi\n2. Arsip Kepegawaian",
                'specific_data' => [
                    'ref_letter_number' => '456/UN/FASILKOM/IX/2026',
                    'ref_letter_date' => '28 September 2026',
                    'ref_letter_subject' => 'Permohonan Kerja Praktik / Magang Mahasiswa',
                ],
                'user_id' => $userId,
            ],
            [
                'title' => 'Template Surat Tugas Pendampingan Teknis Implementasi Sistem',
                'type' => 'tugas',
                'reference_number' => '005/ST/X/2026',
                'regarding' => 'Surat Tugas Pendampingan Teknis Aplikasi',
                'attachment' => '-',
                'letter_date' => $today,
                'letter_place' => 'Jakarta',
                'to_name' => 'Ahmad Fauzi, S.Kom',
                'to_address' => 'Analis Sistem Informasi',
                'opening_salutation' => 'Dengan hormat,',
                'opening_text' => 'Dalam rangka percepatan implementasi dan digitalisasi administrasi persuratan, pimpinan memberikan mandat penugasan kedinasan.',
                'content' => "1. Melaksanakan pendampingan teknis dan bimbingan operasional aplikasi persuratan pada Kantor Cabang Wilayah.\n2. Melakukan evaluasi kesiapan sarana jaringan dan verifikasi basis data pengguna.\n3. Melaporkan hasil pelaksanaan penugasan kepada pimpinan secara tertulis selambat-lambatnya 3 (tiga) hari kerja setelah tugas selesai.",
                'closing_text' => 'Demikian surat tugas ini dibuat untuk dilaksanakan dengan sebaik-baiknya dan penuh rasa tanggung jawab.',
                'closing_salutation' => 'Pemberi Tugas,',
                'signer_position' => 'Kepala Instansi',
                'signer_name' => 'M. Iqbal Effendi, S.T., M.Kom',
                'signer_identity_number' => '19820519 200801 1 007',
                'copy_to' => "1. Bagian Keuangan (untuk proses SPPD)\n2. Arsip Kepegawaian",
                'specific_data' => [
                    'basis' => 'Surat Keputusan Pimpinan Nomor 055/SK/IX/2026 tentang Akselerasi Transformasi Digital Pelayanan.',
                    'assigned_name' => 'Ahmad Fauzi, S.Kom',
                    'assigned_nip' => '19880412 201403 1 002',
                    'assigned_rank' => 'Penata Muda Tk. I (III/b)',
                    'assigned_position' => 'Pranata Komputer / Analis Sistem Informasi',
                    'task_date' => '20 s/d 23 Oktober 2026',
                    'task_location' => 'Kantor Cabang Wilayah I dan Unit Pelayanan Terpadu',
                ],
                'user_id' => $userId,
            ],
        ];

        foreach ($templates as $item) {
            LetterTemplate::firstOrCreate(
                ['reference_number' => $item['reference_number']],
                $item
            );
        }
    }
}
