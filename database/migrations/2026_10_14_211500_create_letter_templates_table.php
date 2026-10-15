<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('letter_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Judul / Nama Template');
            $table->string('type')->comment('Tipe: permohonan, pemberitahuan, undangan, balasan, tugas');
            $table->string('reference_number')->nullable()->comment('Nomor Surat');
            $table->string('regarding')->nullable()->comment('Perihal / Hal');
            $table->string('attachment')->nullable()->comment('Lampiran');
            $table->date('letter_date')->nullable()->comment('Tanggal Surat');
            $table->string('letter_place')->nullable()->comment('Tempat/Kota Pembuatan Surat');
            $table->string('to_name')->nullable()->comment('Kepada Yth.');
            $table->text('to_address')->nullable()->comment('Alamat Tujuan');
            $table->string('opening_salutation')->nullable()->default('Dengan hormat,');
            $table->text('opening_text')->nullable()->comment('Paragraf Pembuka');
            $table->text('content')->nullable()->comment('Isi Surat / Poin Utama');
            $table->text('closing_text')->nullable()->comment('Paragraf Penutup');
            $table->string('closing_salutation')->nullable()->default('Hormat kami,');
            $table->string('signer_position')->nullable()->comment('Jabatan Penandatangan');
            $table->string('signer_name')->nullable()->comment('Nama Penandatangan');
            $table->string('signer_identity_number')->nullable()->comment('NIP / NIDN / NIK Penandatangan');
            $table->text('copy_to')->nullable()->comment('Tembusan (opsional)');
            $table->json('specific_data')->nullable()->comment('Data spesifik per jenis surat');
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_templates');
    }
};
