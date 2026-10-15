@extends('layout.main')

@push('style')
    <style>
        .paper-preview {
            background: #ffffff;
            width: 100%;
            max-width: 850px;
            margin: 0 auto;
            padding: 45px 55px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
            font-family: 'Times New Roman', Times, serif;
            color: #111;
            line-height: 1.5;
            font-size: 14px;
        }

        .paper-kop-title {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .paper-kop-divider {
            border-bottom: 3px double #000;
            margin: 15px 0 25px 0;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            .paper-preview, .paper-preview * {
                visibility: visible;
            }
            .paper-preview {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                max-width: 100%;
                box-shadow: none;
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="no-print">
        <x-breadcrumb
            :values="[__('menu.template.menu'), __('menu.template.list'), $template->title]">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('template.download', $template) }}" class="btn btn-info text-white shadow-sm">
                    <i class="bx bxs-file-doc me-1"></i> Unduh Word (.docx)
                </a>
                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="bx bx-printer me-1"></i> Cetak
                </button>
                <a href="{{ route('template.edit', $template) }}" class="btn btn-warning">
                    <i class="bx bx-edit-alt me-1"></i> Edit
                </a>
                <a href="{{ route('template.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
        </x-breadcrumb>

        <!-- Notification Banner -->
        <div class="alert alert-success d-flex align-items-center justify-content-between mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bx bx-check-circle fs-3 me-2"></i>
                <div>
                    <strong>Pratinjau Template:</strong> Dokumen siap digunakan. Klik <strong>Unduh Word (.docx)</strong> untuk mengunduh template Microsoft Word dengan tata letak resmi.
                </div>
            </div>
            <a href="{{ route('template.download', $template) }}" class="btn btn-sm btn-success">
                <i class="bx bxs-download me-1"></i> Unduh Sekarang
            </a>
        </div>
    </div>

    <!-- Paper Document Container -->
    <div class="paper-preview mb-5">
        @php
            $specific = $template->specific_data ?? [];
            $instName = $config['institution_name'] ?? 'INSTANSI / PERUSAHAAN';
            $instAddress = $config['institution_address'] ?? 'Alamat Kantor Instansi';
            $instPhone = $config['institution_phone'] ?? '-';
            $instEmail = $config['institution_email'] ?? '-';
            $logoPath = file_exists(public_path('logo-black.png')) ? asset('logo-black.png') : null;
        @endphp

        <!-- 1. Kop Surat Resmi -->
        <div class="d-flex align-items-center justify-content-center text-center pb-2">
            @if($logoPath)
                <div class="pe-3">
                    <img src="{{ $logoPath }}" alt="Logo" style="height: 70px; width: auto; object-fit: contain;">
                </div>
            @endif
            <div class="flex-grow-1 text-center">
                <div class="paper-kop-title text-uppercase">{{ $instName }}</div>
                <div style="font-size: 13px;">{{ $instAddress }}</div>
                <div style="font-size: 12px; color: #444;">Telepon: {{ $instPhone }} &nbsp;|&nbsp; Email: {{ $instEmail }}</div>
            </div>
        </div>

        <div class="paper-kop-divider"></div>

        <!-- 2. Body Surat -->
        @if($template->type === 'tugas')
            <!-- Layout Surat Tugas -->
            <div class="text-center my-4">
                <h5 class="fw-bold mb-1 text-decoration-underline" style="font-size: 16px;">SURAT TUGAS</h5>
                <div>Nomor: {{ $template->reference_number ?: '-' }}</div>
            </div>

            @if(!empty($specific['basis']))
                <div class="row mb-3">
                    <div class="col-2 fw-bold">Dasar :</div>
                    <div class="col-10 text-justify">{!! nl2br(e($specific['basis'])) !!}</div>
                </div>
            @endif

            <div class="text-center fw-bold my-3" style="letter-spacing: 1px;">MEMERINTAHKAN:</div>

            <div class="row mb-3">
                <div class="col-2 fw-bold">Kepada :</div>
                <div class="col-10">
                    <table class="table table-borderless table-sm p-0 m-0" style="font-size: 14px;">
                        <tr>
                            <td style="width: 150px; padding: 2px 0;">Nama</td>
                            <td style="width: 20px; padding: 2px 0;">:</td>
                            <td style="padding: 2px 0;" class="fw-bold">{{ $specific['assigned_name'] ?? ($template->to_name ?: '-') }}</td>
                        </tr>
                        @if(!empty($specific['assigned_nip']))
                            <tr>
                                <td style="padding: 2px 0;">NIP / Identitas</td>
                                <td style="padding: 2px 0;">:</td>
                                <td style="padding: 2px 0;">{{ $specific['assigned_nip'] }}</td>
                            </tr>
                        @endif
                        @if(!empty($specific['assigned_rank']))
                            <tr>
                                <td style="padding: 2px 0;">Pangkat / Golongan</td>
                                <td style="padding: 2px 0;">:</td>
                                <td style="padding: 2px 0;">{{ $specific['assigned_rank'] }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td style="padding: 2px 0;">Jabatan</td>
                            <td style="padding: 2px 0;">:</td>
                            <td style="padding: 2px 0;">{{ $specific['assigned_position'] ?? ($template->to_address ?: '-') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-2 fw-bold">Untuk :</div>
                <div class="col-10">
                    @if($template->content)
                        <div class="text-justify mb-2">{!! nl2br(e($template->content)) !!}</div>
                    @endif

                    @if(!empty($specific['task_date']) || !empty($specific['task_location']))
                        <table class="table table-borderless table-sm p-0 m-0" style="font-size: 14px;">
                            @if(!empty($specific['task_date']))
                                <tr>
                                    <td style="width: 150px; padding: 2px 0;">Waktu</td>
                                    <td style="width: 20px; padding: 2px 0;">:</td>
                                    <td style="padding: 2px 0;">{{ $specific['task_date'] }}</td>
                                </tr>
                            @endif
                            @if(!empty($specific['task_location']))
                                <tr>
                                    <td style="padding: 2px 0;">Tempat</td>
                                    <td style="padding: 2px 0;">:</td>
                                    <td style="padding: 2px 0;">{{ $specific['task_location'] }}</td>
                                </tr>
                            @endif
                        </table>
                    @endif
                </div>
            </div>

            <div class="text-justify my-4">
                {!! nl2br(e($template->closing_text ?: 'Demikian surat tugas ini dibuat untuk dilaksanakan dengan sebaik-baiknya dan penuh rasa tanggung jawab.')) !!}
            </div>

        @else
            <!-- Layout Standar: Permohonan, Pemberitahuan, Undangan, Balasan -->
            <div class="d-flex justify-content-between mb-4">
                <div>
                    <table class="table table-borderless table-sm p-0 m-0" style="font-size: 14px;">
                        <tr>
                            <td style="width: 80px; padding: 2px 0;">Nomor</td>
                            <td style="width: 15px; padding: 2px 0;">:</td>
                            <td style="padding: 2px 0;">{{ $template->reference_number ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;">Lampiran</td>
                            <td style="padding: 2px 0;">:</td>
                            <td style="padding: 2px 0;">{{ $template->attachment ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0;">Perihal</td>
                            <td style="padding: 2px 0;">:</td>
                            <td style="padding: 2px 0;" class="fw-bold">{{ $template->regarding ?: $template->title }}</td>
                        </tr>
                    </table>
                </div>
                <div class="text-end">
                    <div>{{ $template->letter_place ?: 'Jakarta' }}, {{ $template->formatted_letter_date }}</div>
                </div>
            </div>

            <!-- Kepada Yth -->
            <div class="mb-4">
                <div>Kepada Yth.</div>
                <div class="fw-bold">{{ $template->to_name ?: 'Penerima Surat' }}</div>
                <div>{!! nl2br(e($template->to_address ?: 'di Tempat')) !!}</div>
            </div>

            <!-- Salam Pembuka -->
            <div class="mb-3">
                {{ $template->opening_salutation ?: 'Dengan hormat,' }}
            </div>

            <!-- Paragraf Pembuka -->
            @if($template->opening_text)
                <div class="text-justify mb-3" style="text-indent: 30px;">
                    {!! nl2br(e($template->opening_text)) !!}
                </div>
            @endif

            <!-- Rincian Khusus Per Jenis Surat -->
            @if($template->type === 'undangan' && (!empty($specific['event_date']) || !empty($specific['event_location']) || !empty($specific['event_agenda'])))
                <div class="ps-4 mb-3">
                    <table class="table table-borderless table-sm p-0 m-0" style="font-size: 14px;">
                        @if(!empty($specific['event_date']))
                            <tr>
                                <td style="width: 140px; padding: 3px 0;">Hari / Tanggal</td>
                                <td style="width: 15px; padding: 3px 0;">:</td>
                                <td style="padding: 3px 0;">{{ $specific['event_date'] }}</td>
                            </tr>
                        @endif
                        @if(!empty($specific['event_time']))
                            <tr>
                                <td style="padding: 3px 0;">Waktu / Pukul</td>
                                <td style="padding: 3px 0;">:</td>
                                <td style="padding: 3px 0;">{{ $specific['event_time'] }}</td>
                            </tr>
                        @endif
                        @if(!empty($specific['event_location']))
                            <tr>
                                <td style="padding: 3px 0;">Tempat</td>
                                <td style="padding: 3px 0;">:</td>
                                <td style="padding: 3px 0;">{{ $specific['event_location'] }}</td>
                            </tr>
                        @endif
                        @if(!empty($specific['event_agenda']))
                            <tr>
                                <td style="padding: 3px 0; vertical-align: top;">Acara / Agenda</td>
                                <td style="padding: 3px 0; vertical-align: top;">:</td>
                                <td style="padding: 3px 0;">{!! nl2br(e($specific['event_agenda'])) !!}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            @elseif($template->type === 'balasan' && (!empty($specific['ref_letter_number']) || !empty($specific['ref_letter_subject'])))
                <div class="ps-4 mb-3">
                    <table class="table table-borderless table-sm p-0 m-0" style="font-size: 14px;">
                        @if(!empty($specific['ref_letter_number']))
                            <tr>
                                <td style="width: 160px; padding: 2px 0;">Merujuk Surat Nomor</td>
                                <td style="width: 15px; padding: 2px 0;">:</td>
                                <td style="padding: 2px 0;">{{ $specific['ref_letter_number'] }}</td>
                            </tr>
                        @endif
                        @if(!empty($specific['ref_letter_date']))
                            <tr>
                                <td style="padding: 2px 0;">Tanggal Surat</td>
                                <td style="padding: 2px 0;">:</td>
                                <td style="padding: 2px 0;">{{ $specific['ref_letter_date'] }}</td>
                            </tr>
                        @endif
                        @if(!empty($specific['ref_letter_subject']))
                            <tr>
                                <td style="padding: 2px 0;">Perihal Surat</td>
                                <td style="padding: 2px 0;">:</td>
                                <td style="padding: 2px 0;">{{ $specific['ref_letter_subject'] }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            @endif

            <!-- Isi Utama -->
            @if($template->content)
                <div class="text-justify mb-3">
                    {!! nl2br(e($template->content)) !!}
                </div>
            @endif

            <!-- Paragraf Penutup -->
            @if($template->closing_text)
                <div class="text-justify mb-4" style="text-indent: 30px;">
                    {!! nl2br(e($template->closing_text)) !!}
                </div>
            @endif
        @endif

        <!-- 3. Tanda Tangan (Signature Block) -->
        <div class="row mt-5 pt-3">
            <div class="col-6"></div>
            <div class="col-6 text-center">
                @if($template->type !== 'tugas')
                    <div class="mb-1">{{ $template->closing_salutation ?: 'Hormat kami,' }}</div>
                @else
                    <div class="mb-1">{{ $template->letter_place ?: 'Jakarta' }}, {{ $template->formatted_letter_date }}</div>
                @endif
                <div class="fw-bold">{{ $template->signer_position ?: 'Pimpinan Instansi' }}</div>

                <!-- Signature Space -->
                <div style="height: 80px;"></div>

                <div class="fw-bold text-decoration-underline">{{ $template->signer_name ?: 'Nama Lengkap' }}</div>
                @if($template->signer_identity_number)
                    <div style="font-size: 13px;">NIP. {{ $template->signer_identity_number }}</div>
                @endif
            </div>
        </div>

        <!-- 4. Tembusan (Carbon Copy) -->
        @if($template->copy_to)
            <div class="mt-5 pt-3 border-top" style="font-size: 12px; color: #444;">
                <div class="fw-bold">Tembusan:</div>
                <div>{!! nl2br(e($template->copy_to)) !!}</div>
            </div>
        @endif
    </div>
@endsection
