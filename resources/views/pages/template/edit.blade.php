@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('menu.template.menu'), __('menu.template.list'), __('menu.general.edit')]">
        <div class="d-flex gap-2">
            <a href="{{ route('template.download', $template) }}" class="btn btn-info text-white">
                <i class="bx bxs-file-doc me-1"></i> Unduh Word (.docx)
            </a>
            <a href="{{ route('template.show', $template) }}" class="btn btn-outline-secondary">
                <i class="bx bx-show me-1"></i> Lihat Pratinjau
            </a>
        </div>
    </x-breadcrumb>

    <div class="row">
        <div class="col-12">
            <form action="{{ route('template.update', $template) }}" method="POST" id="template-edit-form">
                @csrf
                @method('PUT')

                @php
                    $specific = $template->specific_data ?? [];
                @endphp

                <!-- Card 1: Pilihan Jenis Surat -->
                <div class="card mb-4">
                    <div class="card-header border-bottom py-3">
                        <h5 class="card-title mb-0"><i class="bx bx-category me-2"></i>1. Jenis Surat</h5>
                    </div>
                    <div class="card-body pt-3">
                        <div class="row g-2">
                            @foreach($types as $typeEnum)
                                <div class="col-sm-6 col-md">
                                    <input type="radio"
                                           class="btn-check type-radio"
                                           name="type"
                                           id="type-{{ $typeEnum->value }}"
                                           value="{{ $typeEnum->value }}"
                                           autocomplete="off"
                                           @checked(old('type', $template->type) === $typeEnum->value)>
                                    <label class="btn btn-outline-{{ $typeEnum->badgeColor() }} w-100 d-flex flex-column align-items-center py-3"
                                           for="type-{{ $typeEnum->value }}">
                                        <i class="bx {{ $typeEnum->icon() }} fs-2 mb-1"></i>
                                        <span class="fw-bold">{{ $typeEnum->label() }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Card 2: Informasi Dasar & Nomor Surat -->
                <div class="card mb-4">
                    <div class="card-header border-bottom py-3">
                        <h5 class="card-title mb-0"><i class="bx bx-detail me-2"></i>2. Informasi Pokok & Penomoran</h5>
                    </div>
                    <div class="card-body pt-3">
                        <div class="row g-3">
                            <div class="col-12 col-md-8">
                                <label for="title" class="form-label fw-semibold">Judul / Nama Template <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('title') is-invalid @enderror"
                                       id="title"
                                       name="title"
                                       value="{{ old('title', $template->title) }}"
                                       required>
                                <span class="error invalid-feedback">{{ $errors->first('title') }}</span>
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="reference_number" class="form-label fw-semibold">Nomor Surat</label>
                                <input type="text"
                                       class="form-control @error('reference_number') is-invalid @enderror"
                                       id="reference_number"
                                       name="reference_number"
                                       value="{{ old('reference_number', $template->reference_number) }}">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="regarding" class="form-label fw-semibold">Perihal / Hal</label>
                                <input type="text"
                                       class="form-control @error('regarding') is-invalid @enderror"
                                       id="regarding"
                                       name="regarding"
                                       value="{{ old('regarding', $template->regarding) }}">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="attachment" class="form-label fw-semibold">Lampiran</label>
                                <input type="text"
                                       class="form-control @error('attachment') is-invalid @enderror"
                                       id="attachment"
                                       name="attachment"
                                       value="{{ old('attachment', $template->attachment) }}">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="letter_place" class="form-label fw-semibold">Tempat / Kota Surat</label>
                                <input type="text"
                                       class="form-control @error('letter_place') is-invalid @enderror"
                                       id="letter_place"
                                       name="letter_place"
                                       value="{{ old('letter_place', $template->letter_place) }}">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="letter_date" class="form-label fw-semibold">Tanggal Surat</label>
                                <input type="date"
                                       class="form-control @error('letter_date') is-invalid @enderror"
                                       id="letter_date"
                                       name="letter_date"
                                       value="{{ old('letter_date', optional($template->letter_date)->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Tujuan Surat -->
                <div class="card mb-4" id="section-recipient">
                    <div class="card-header border-bottom py-3">
                        <h5 class="card-title mb-0"><i class="bx bx-user-pin me-2"></i>3. Tujuan / Penerima Surat</h5>
                    </div>
                    <div class="card-body pt-3">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="to_name" class="form-label fw-semibold" id="label-to_name">Kepada Yth. (Nama / Jabatan)</label>
                                <input type="text"
                                       class="form-control @error('to_name') is-invalid @enderror"
                                       id="to_name"
                                       name="to_name"
                                       value="{{ old('to_name', $template->to_name) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="to_address" class="form-label fw-semibold" id="label-to_address">Alamat / Di Tempat</label>
                                <textarea class="form-control @error('to_address') is-invalid @enderror"
                                          id="to_address"
                                          name="to_address"
                                          rows="2">{{ old('to_address', $template->to_address) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Rincian Khusus Berdasarkan Jenis Surat -->
                <div class="card mb-4" id="section-specific">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0" id="specific-card-title"><i class="bx bx-slider-alt me-2"></i>4. Rincian Khusus Jenis Surat</h5>
                        <span class="badge bg-label-{{ $template->type_badge }}" id="specific-type-badge">{{ $template->type_label }}</span>
                    </div>
                    <div class="card-body pt-3">
                        <!-- Permohonan Fields -->
                        <div class="specific-fields {{ $template->type !== 'permohonan' ? 'd-none' : '' }}" id="fields-permohonan">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Hal yang Dimohonkan</label>
                                    <input type="text" class="form-control" name="specific_data[request_item]" id="permohonan-item"
                                           value="{{ old('specific_data.request_item', $specific['request_item'] ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Keperluan / Alasan</label>
                                    <input type="text" class="form-control" name="specific_data[request_reason]" id="permohonan-reason"
                                           value="{{ old('specific_data.request_reason', $specific['request_reason'] ?? '') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Pemberitahuan Fields -->
                        <div class="specific-fields {{ $template->type !== 'pemberitahuan' ? 'd-none' : '' }}" id="fields-pemberitahuan">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Sasaran / Penerima Pemberitahuan</label>
                                    <input type="text" class="form-control" name="specific_data[target_audience]" id="pemberitahuan-target"
                                           value="{{ old('specific_data.target_audience', $specific['target_audience'] ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Waktu / Tanggal Berlaku</label>
                                    <input type="text" class="form-control" name="specific_data[effective_date]" id="pemberitahuan-date"
                                           value="{{ old('specific_data.effective_date', $specific['effective_date'] ?? '') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Undangan Fields -->
                        <div class="specific-fields {{ $template->type !== 'undangan' ? 'd-none' : '' }}" id="fields-undangan">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Hari / Tanggal Acara</label>
                                    <input type="text" class="form-control" name="specific_data[event_date]" id="undangan-date"
                                           value="{{ old('specific_data.event_date', $specific['event_date'] ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Waktu / Pukul</label>
                                    <input type="text" class="form-control" name="specific_data[event_time]" id="undangan-time"
                                           value="{{ old('specific_data.event_time', $specific['event_time'] ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tempat / Ruangan</label>
                                    <input type="text" class="form-control" name="specific_data[event_location]" id="undangan-location"
                                           value="{{ old('specific_data.event_location', $specific['event_location'] ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Agenda / Acara</label>
                                    <textarea class="form-control" name="specific_data[event_agenda]" id="undangan-agenda" rows="2">{{ old('specific_data.event_agenda', $specific['event_agenda'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Balasan Fields -->
                        <div class="specific-fields {{ $template->type !== 'balasan' ? 'd-none' : '' }}" id="fields-balasan">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Merujuk Surat Nomor</label>
                                    <input type="text" class="form-control" name="specific_data[ref_letter_number]" id="balasan-ref-no"
                                           value="{{ old('specific_data.ref_letter_number', $specific['ref_letter_number'] ?? '') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Tanggal Surat yang Dibalas</label>
                                    <input type="text" class="form-control" name="specific_data[ref_letter_date]" id="balasan-ref-date"
                                           value="{{ old('specific_data.ref_letter_date', $specific['ref_letter_date'] ?? '') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Perihal Surat yang Dibalas</label>
                                    <input type="text" class="form-control" name="specific_data[ref_letter_subject]" id="balasan-ref-subject"
                                           value="{{ old('specific_data.ref_letter_subject', $specific['ref_letter_subject'] ?? '') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Tugas Fields -->
                        <div class="specific-fields {{ $template->type !== 'tugas' ? 'd-none' : '' }}" id="fields-tugas">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Dasar Penugasan</label>
                                    <input type="text" class="form-control" name="specific_data[basis]" id="tugas-basis"
                                           value="{{ old('specific_data.basis', $specific['basis'] ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nama yang Ditugaskan</label>
                                    <input type="text" class="form-control" name="specific_data[assigned_name]" id="tugas-name"
                                           value="{{ old('specific_data.assigned_name', $specific['assigned_name'] ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">NIP / Identitas yang Ditugaskan</label>
                                    <input type="text" class="form-control" name="specific_data[assigned_nip]" id="tugas-nip"
                                           value="{{ old('specific_data.assigned_nip', $specific['assigned_nip'] ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Pangkat / Golongan</label>
                                    <input type="text" class="form-control" name="specific_data[assigned_rank]" id="tugas-rank"
                                           value="{{ old('specific_data.assigned_rank', $specific['assigned_rank'] ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Jabatan yang Ditugaskan</label>
                                    <input type="text" class="form-control" name="specific_data[assigned_position]" id="tugas-position"
                                           value="{{ old('specific_data.assigned_position', $specific['assigned_position'] ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Waktu / Periode Pelaksanaan Tugas</label>
                                    <input type="text" class="form-control" name="specific_data[task_date]" id="tugas-date"
                                           value="{{ old('specific_data.task_date', $specific['task_date'] ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Lokasi / Tempat Pelaksanaan Tugas</label>
                                    <input type="text" class="form-control" name="specific_data[task_location]" id="tugas-location"
                                           value="{{ old('specific_data.task_location', $specific['task_location'] ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 5: Teks Surat (Pembuka, Isi, Penutup) -->
                <div class="card mb-4">
                    <div class="card-header border-bottom py-3">
                        <h5 class="card-title mb-0"><i class="bx bx-edit me-2"></i>5. Teks Surat & Isi Utama</h5>
                    </div>
                    <div class="card-body pt-3">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="opening_salutation" class="form-label fw-semibold">Salam Pembuka</label>
                                <input type="text"
                                       class="form-control"
                                       id="opening_salutation"
                                       name="opening_salutation"
                                       value="{{ old('opening_salutation', $template->opening_salutation) }}">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="closing_salutation" class="form-label fw-semibold">Salam Penutup</label>
                                <input type="text"
                                       class="form-control"
                                       id="closing_salutation"
                                       name="closing_salutation"
                                       value="{{ old('closing_salutation', $template->closing_salutation) }}">
                            </div>

                            <div class="col-12">
                                <label for="opening_text" class="form-label fw-semibold">Paragraf Pembuka</label>
                                <textarea class="form-control"
                                          id="opening_text"
                                          name="opening_text"
                                          rows="3">{{ old('opening_text', $template->opening_text) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label for="content" class="form-label fw-semibold">Isi Utama Surat / Perintah Penugasan</label>
                                <textarea class="form-control"
                                          id="content"
                                          name="content"
                                          rows="5">{{ old('content', $template->content) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label for="closing_text" class="form-label fw-semibold">Paragraf Penutup</label>
                                <textarea class="form-control"
                                          id="closing_text"
                                          name="closing_text"
                                          rows="3">{{ old('closing_text', $template->closing_text) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 6: Penandatangan & Tembusan -->
                <div class="card mb-4">
                    <div class="card-header border-bottom py-3">
                        <h5 class="card-title mb-0"><i class="bx bx-pen me-2"></i>6. Penandatangan & Tembusan</h5>
                    </div>
                    <div class="card-body pt-3">
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label for="signer_position" class="form-label fw-semibold">Jabatan Penandatangan</label>
                                <input type="text"
                                       class="form-control"
                                       id="signer_position"
                                       name="signer_position"
                                       value="{{ old('signer_position', $template->signer_position) }}">
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="signer_name" class="form-label fw-semibold">Nama Lengkap Penandatangan</label>
                                <input type="text"
                                       class="form-control"
                                       id="signer_name"
                                       name="signer_name"
                                       value="{{ old('signer_name', $template->signer_name) }}">
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="signer_identity_number" class="form-label fw-semibold">NIP / NIDN / NIK</label>
                                <input type="text"
                                       class="form-control"
                                       id="signer_identity_number"
                                       name="signer_identity_number"
                                       value="{{ old('signer_identity_number', $template->signer_identity_number) }}">
                            </div>

                            <div class="col-12">
                                <label for="copy_to" class="form-label fw-semibold">Tembusan (Opsional)</label>
                                <textarea class="form-control"
                                          id="copy_to"
                                          name="copy_to"
                                          rows="2">{{ old('copy_to', $template->copy_to) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Sticky Bar -->
                <div class="card bg-light shadow-sm mb-4">
                    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <span class="text-muted small">Perubahan akan disimpan pada sistem dan siap diunduh kembali dalam format Word (.docx).</span>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('template.show', $template) }}" class="btn btn-outline-secondary">
                                <i class="bx bx-x me-1"></i> Batal
                            </a>
                            <button type="submit" name="action" value="save" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Perbarui Template
                            </button>
                            <button type="submit" name="action" value="save_and_download" class="btn btn-success">
                                <i class="bx bxs-file-doc me-1"></i> Perbarui & Unduh Word (.docx)
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function updateTypeSection(selectedType) {
            document.querySelectorAll('.specific-fields').forEach(el => el.classList.add('d-none'));

            const targetGroup = document.getElementById('fields-' + selectedType);
            if (targetGroup) {
                targetGroup.classList.remove('d-none');
            }

            const badge = document.getElementById('specific-type-badge');
            const typeLabels = {
                'permohonan': 'Surat Permohonan',
                'pemberitahuan': 'Surat Pemberitahuan',
                'undangan': 'Surat Undangan',
                'balasan': 'Surat Balasan',
                'tugas': 'Surat Tugas'
            };
            const typeColors = {
                'permohonan': 'primary',
                'pemberitahuan': 'warning',
                'undangan': 'success',
                'balasan': 'info',
                'tugas': 'danger'
            };

            if (badge) {
                badge.textContent = typeLabels[selectedType] || selectedType;
                badge.className = 'badge bg-label-' + (typeColors[selectedType] || 'primary');
            }

            const labelToName = document.getElementById('label-to_name');
            const labelToAddress = document.getElementById('label-to_address');
            if (selectedType === 'tugas') {
                if (labelToName) labelToName.textContent = 'Penerima Tugas / Yang Ditugaskan';
                if (labelToAddress) labelToAddress.textContent = 'Jabatan / Instansi Penerima Tugas';
            } else {
                if (labelToName) labelToName.textContent = 'Kepada Yth. (Nama / Jabatan)';
                if (labelToAddress) labelToAddress.textContent = 'Alamat / Di Tempat';
            }
        }

        document.querySelectorAll('.type-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                updateTypeSection(this.value);
            });
        });
    </script>
@endpush
