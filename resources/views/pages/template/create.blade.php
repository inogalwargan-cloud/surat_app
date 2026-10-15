@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('menu.template.menu'), __('menu.template.list'), __('menu.general.create')]">
        <a href="{{ route('template.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali
        </a>
    </x-breadcrumb>

    <div class="row">
        <div class="col-12">
            <!-- Alert Banner -->
            <div class="alert alert-primary d-flex align-items-center justify-content-between mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bx bx-info-circle fs-3 me-2"></i>
                    <div>
                        <strong>Buat Template Surat Resmi:</strong> Pilih jenis surat, isi formulir di bawah ini, lalu simpan atau langsung unduh dalam format <strong>Microsoft Word (.docx)</strong>.
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-primary" id="btn-load-preset">
                    <i class="bx bx-magic-wand me-1"></i> Gunakan Contoh Standar
                </button>
            </div>

            <form action="{{ route('template.store') }}" method="POST" id="template-form">
                @csrf

                <!-- Card 1: Pilihan Jenis Surat -->
                <div class="card mb-4">
                    <div class="card-header border-bottom py-3">
                        <h5 class="card-title mb-0"><i class="bx bx-category me-2"></i>1. Pilih Jenis Surat</h5>
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
                                           @checked(old('type', $defaultType) === $typeEnum->value)>
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
                                       placeholder="Contoh: Surat Permohonan Izin Tempat dan Sarana"
                                       value="{{ old('title') }}"
                                       required>
                                <span class="error invalid-feedback">{{ $errors->first('title') }}</span>
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="reference_number" class="form-label fw-semibold">Nomor Surat</label>
                                <input type="text"
                                       class="form-control @error('reference_number') is-invalid @enderror"
                                       id="reference_number"
                                       name="reference_number"
                                       placeholder="Contoh: 001/SP/X/2026"
                                       value="{{ old('reference_number') }}">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="regarding" class="form-label fw-semibold">Perihal / Hal</label>
                                <input type="text"
                                       class="form-control @error('regarding') is-invalid @enderror"
                                       id="regarding"
                                       name="regarding"
                                       placeholder="Contoh: Permohonan Izin Penggunaan Gedung"
                                       value="{{ old('regarding') }}">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="attachment" class="form-label fw-semibold">Lampiran</label>
                                <input type="text"
                                       class="form-control @error('attachment') is-invalid @enderror"
                                       id="attachment"
                                       name="attachment"
                                       placeholder="Contoh: 1 (satu) Berkas Proposal / -"
                                       value="{{ old('attachment', '-') }}">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="letter_place" class="form-label fw-semibold">Tempat / Kota Surat</label>
                                <input type="text"
                                       class="form-control @error('letter_place') is-invalid @enderror"
                                       id="letter_place"
                                       name="letter_place"
                                       placeholder="Contoh: Jakarta"
                                       value="{{ old('letter_place', $defaultCity) }}">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="letter_date" class="form-label fw-semibold">Tanggal Surat</label>
                                <input type="date"
                                       class="form-control @error('letter_date') is-invalid @enderror"
                                       id="letter_date"
                                       name="letter_date"
                                       value="{{ old('letter_date', $todayDate) }}">
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
                                       placeholder="Contoh: Kepala Dinas Pemuda dan Olahraga"
                                       value="{{ old('to_name') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="to_address" class="form-label fw-semibold" id="label-to_address">Alamat / Di Tempat</label>
                                <textarea class="form-control @error('to_address') is-invalid @enderror"
                                          id="to_address"
                                          name="to_address"
                                          rows="2"
                                          placeholder="Contoh: Jalan Pemuda No. 45&#10;di Tempat">{{ old('to_address') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Rincian Khusus Berdasarkan Jenis Surat -->
                <div class="card mb-4" id="section-specific">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0" id="specific-card-title"><i class="bx bx-slider-alt me-2"></i>4. Rincian Khusus Jenis Surat</h5>
                        <span class="badge bg-label-primary" id="specific-type-badge">Surat Permohonan</span>
                    </div>
                    <div class="card-body pt-3">
                        <!-- Permohonan Fields -->
                        <div class="specific-fields" id="fields-permohonan">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Hal yang Dimohonkan</label>
                                    <input type="text" class="form-control" name="specific_data[request_item]" id="permohonan-item"
                                           placeholder="Contoh: Izin Penggunaan Gedung Serbaguna"
                                           value="{{ old('specific_data.request_item') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Keperluan / Alasan</label>
                                    <input type="text" class="form-control" name="specific_data[request_reason]" id="permohonan-reason"
                                           placeholder="Contoh: Pelatihan Kepemimpinan Pemuda 2026"
                                           value="{{ old('specific_data.request_reason') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Pemberitahuan Fields -->
                        <div class="specific-fields d-none" id="fields-pemberitahuan">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Sasaran / Penerima Pemberitahuan</label>
                                    <input type="text" class="form-control" name="specific_data[target_audience]" id="pemberitahuan-target"
                                           placeholder="Contoh: Seluruh Karyawan dan Pengguna Layanan"
                                           value="{{ old('specific_data.target_audience') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Waktu / Tanggal Berlaku</label>
                                    <input type="text" class="form-control" name="specific_data[effective_date]" id="pemberitahuan-date"
                                           placeholder="Contoh: Mulai 24 Oktober 2026 s/d selesai"
                                           value="{{ old('specific_data.effective_date') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Undangan Fields -->
                        <div class="specific-fields d-none" id="fields-undangan">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Hari / Tanggal Acara</label>
                                    <input type="text" class="form-control" name="specific_data[event_date]" id="undangan-date"
                                           placeholder="Contoh: Rabu, 22 Oktober 2026"
                                           value="{{ old('specific_data.event_date') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Waktu / Pukul</label>
                                    <input type="text" class="form-control" name="specific_data[event_time]" id="undangan-time"
                                           placeholder="Contoh: Pukul 09.00 WIB s/d 12.30 WIB"
                                           value="{{ old('specific_data.event_time') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tempat / Ruangan</label>
                                    <input type="text" class="form-control" name="specific_data[event_location]" id="undangan-location"
                                           placeholder="Contoh: Ruang Rapat Utama Lantai 2"
                                           value="{{ old('specific_data.event_location') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Agenda / Acara</label>
                                    <textarea class="form-control" name="specific_data[event_agenda]" id="undangan-agenda" rows="2"
                                              placeholder="Contoh: 1. Evaluasi Capaian Kinerja&#10;2. Pembahasan Program">{{ old('specific_data.event_agenda') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Balasan Fields -->
                        <div class="specific-fields d-none" id="fields-balasan">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Merujuk Surat Nomor</label>
                                    <input type="text" class="form-control" name="specific_data[ref_letter_number]" id="balasan-ref-no"
                                           placeholder="Contoh: 456/UN/FASILKOM/IX/2026"
                                           value="{{ old('specific_data.ref_letter_number') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Tanggal Surat yang Dibalas</label>
                                    <input type="text" class="form-control" name="specific_data[ref_letter_date]" id="balasan-ref-date"
                                           placeholder="Contoh: 28 September 2026"
                                           value="{{ old('specific_data.ref_letter_date') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Perihal Surat yang Dibalas</label>
                                    <input type="text" class="form-control" name="specific_data[ref_letter_subject]" id="balasan-ref-subject"
                                           placeholder="Contoh: Permohonan Magang Mahasiswa"
                                           value="{{ old('specific_data.ref_letter_subject') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Tugas Fields -->
                        <div class="specific-fields d-none" id="fields-tugas">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Dasar Penugasan</label>
                                    <input type="text" class="form-control" name="specific_data[basis]" id="tugas-basis"
                                           placeholder="Contoh: Surat Keputusan Pimpinan Nomor 055/SK/IX/2026 tentang Tim Percepatan Digitalisasi"
                                           value="{{ old('specific_data.basis') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nama yang Ditugaskan</label>
                                    <input type="text" class="form-control" name="specific_data[assigned_name]" id="tugas-name"
                                           placeholder="Contoh: Ahmad Fauzi, S.Kom"
                                           value="{{ old('specific_data.assigned_name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">NIP / Identitas yang Ditugaskan</label>
                                    <input type="text" class="form-control" name="specific_data[assigned_nip]" id="tugas-nip"
                                           placeholder="Contoh: 19880412 201403 1 002"
                                           value="{{ old('specific_data.assigned_nip') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Pangkat / Golongan</label>
                                    <input type="text" class="form-control" name="specific_data[assigned_rank]" id="tugas-rank"
                                           placeholder="Contoh: Penata Muda Tk. I (III/b)"
                                           value="{{ old('specific_data.assigned_rank') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Jabatan yang Ditugaskan</label>
                                    <input type="text" class="form-control" name="specific_data[assigned_position]" id="tugas-position"
                                           placeholder="Contoh: Analis Sistem Informasi"
                                           value="{{ old('specific_data.assigned_position') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Waktu / Periode Pelaksanaan Tugas</label>
                                    <input type="text" class="form-control" name="specific_data[task_date]" id="tugas-date"
                                           placeholder="Contoh: 20 s/d 23 Oktober 2026"
                                           value="{{ old('specific_data.task_date') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Lokasi / Tempat Pelaksanaan Tugas</label>
                                    <input type="text" class="form-control" name="specific_data[task_location]" id="tugas-location"
                                           placeholder="Contoh: Kantor Cabang Wilayah I dan Unit Pelaksana Teknis"
                                           value="{{ old('specific_data.task_location') }}">
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
                                       value="{{ old('opening_salutation', 'Dengan hormat,') }}">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="closing_salutation" class="form-label fw-semibold">Salam Penutup</label>
                                <input type="text"
                                       class="form-control"
                                       id="closing_salutation"
                                       name="closing_salutation"
                                       value="{{ old('closing_salutation', 'Hormat kami,') }}">
                            </div>

                            <div class="col-12">
                                <label for="opening_text" class="form-label fw-semibold">Paragraf Pembuka</label>
                                <textarea class="form-control"
                                          id="opening_text"
                                          name="opening_text"
                                          rows="3"
                                          placeholder="Tuliskan kalimat pembuka surat...">{{ old('opening_text') }}</textarea>
                            </div>

                            <div class="col-12">
                                <label for="content" class="form-label fw-semibold">Isi Utama Surat / Perintah Penugasan</label>
                                <textarea class="form-control"
                                          id="content"
                                          name="content"
                                          rows="5"
                                          placeholder="Tuliskan pokok isi surat...">{{ old('content') }}</textarea>
                            </div>

                            <div class="col-12">
                                <label for="closing_text" class="form-label fw-semibold">Paragraf Penutup</label>
                                <textarea class="form-control"
                                          id="closing_text"
                                          name="closing_text"
                                          rows="3"
                                          placeholder="Demikian surat ini kami sampaikan, atas perhatian dan kerja samanya...">{{ old('closing_text') }}</textarea>
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
                                       placeholder="Contoh: Kepala Bagian Umum"
                                       value="{{ old('signer_position') }}">
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="signer_name" class="form-label fw-semibold">Nama Lengkap Penandatangan</label>
                                <input type="text"
                                       class="form-control"
                                       id="signer_name"
                                       name="signer_name"
                                       placeholder="Contoh: M. Iqbal Effendi, S.T., M.Kom"
                                       value="{{ old('signer_name', $defaultPic) }}">
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="signer_identity_number" class="form-label fw-semibold">NIP / NIDN / NIK</label>
                                <input type="text"
                                       class="form-control"
                                       id="signer_identity_number"
                                       name="signer_identity_number"
                                       placeholder="Contoh: 19820519 200801 1 007"
                                       value="{{ old('signer_identity_number') }}">
                            </div>

                            <div class="col-12">
                                <label for="copy_to" class="form-label fw-semibold">Tembusan (Opsional)</label>
                                <textarea class="form-control"
                                          id="copy_to"
                                          name="copy_to"
                                          rows="2"
                                          placeholder="1. Pimpinan Instansi&#10;2. Arsip Bagian Umum">{{ old('copy_to') }}</textarea>
                                <div class="form-text">Gunakan baris baru untuk setiap nomor tembusan.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Sticky Bar -->
                <div class="card bg-light shadow-sm mb-4">
                    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <span class="text-muted small">Pastikan seluruh data yang diperlukan telah terisi dengan benar.</span>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('template.index') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-x me-1"></i> {{ __('menu.general.cancel') }}
                            </a>
                            <button type="submit" name="action" value="save" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan Template
                            </button>
                            <button type="submit" name="action" value="save_and_download" class="btn btn-success">
                                <i class="bx bxs-file-doc me-1"></i> Simpan & Unduh Word (.docx)
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
        const presets = @json($presets);

        function updateTypeSection(selectedType) {
            // Hide all specific field groups
            document.querySelectorAll('.specific-fields').forEach(el => el.classList.add('d-none'));

            // Show selected type's specific fields
            const targetGroup = document.getElementById('fields-' + selectedType);
            if (targetGroup) {
                targetGroup.classList.remove('d-none');
            }

            // Update badge text and color
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

            // Adjust labels for Surat Tugas
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

        // Handle type radio change
        document.querySelectorAll('.type-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                updateTypeSection(this.value);
            });
        });

        // Initialize on page load
        const checkedRadio = document.querySelector('.type-radio:checked');
        if (checkedRadio) {
            updateTypeSection(checkedRadio.value);
        }

        // Handle Load Preset Button
        document.getElementById('btn-load-preset').addEventListener('click', function() {
            const currentType = document.querySelector('.type-radio:checked')?.value || 'permohonan';
            const preset = presets[currentType];

            if (!preset) return;

            document.getElementById('title').value = preset.title || '';
            document.getElementById('reference_number').value = preset.reference_number || '';
            document.getElementById('regarding').value = preset.regarding || '';
            document.getElementById('attachment').value = preset.attachment || '-';
            document.getElementById('letter_place').value = preset.letter_place || 'Jakarta';
            document.getElementById('to_name').value = preset.to_name || '';
            document.getElementById('to_address').value = preset.to_address || '';
            document.getElementById('opening_salutation').value = preset.opening_salutation || 'Dengan hormat,';
            document.getElementById('closing_salutation').value = preset.closing_salutation || 'Hormat kami,';
            document.getElementById('opening_text').value = preset.opening_text || '';
            document.getElementById('content').value = preset.content || '';
            document.getElementById('closing_text').value = preset.closing_text || '';
            document.getElementById('signer_position').value = preset.signer_position || '';
            document.getElementById('signer_name').value = preset.signer_name || '';
            document.getElementById('signer_identity_number').value = preset.signer_identity_number || '';
            document.getElementById('copy_to').value = preset.copy_to || '';

            // Fill specific fields
            if (preset.specific_data) {
                if (currentType === 'permohonan') {
                    document.getElementById('permohonan-item').value = preset.specific_data.request_item || '';
                    document.getElementById('permohonan-reason').value = preset.specific_data.request_reason || '';
                } else if (currentType === 'pemberitahuan') {
                    document.getElementById('pemberitahuan-target').value = preset.specific_data.target_audience || '';
                    document.getElementById('pemberitahuan-date').value = preset.specific_data.effective_date || '';
                } else if (currentType === 'undangan') {
                    document.getElementById('undangan-date').value = preset.specific_data.event_date || '';
                    document.getElementById('undangan-time').value = preset.specific_data.event_time || '';
                    document.getElementById('undangan-location').value = preset.specific_data.event_location || '';
                    document.getElementById('undangan-agenda').value = preset.specific_data.event_agenda || '';
                } else if (currentType === 'balasan') {
                    document.getElementById('balasan-ref-no').value = preset.specific_data.ref_letter_number || '';
                    document.getElementById('balasan-ref-date').value = preset.specific_data.ref_letter_date || '';
                    document.getElementById('balasan-ref-subject').value = preset.specific_data.ref_letter_subject || '';
                } else if (currentType === 'tugas') {
                    document.getElementById('tugas-basis').value = preset.specific_data.basis || '';
                    document.getElementById('tugas-name').value = preset.specific_data.assigned_name || '';
                    document.getElementById('tugas-nip').value = preset.specific_data.assigned_nip || '';
                    document.getElementById('tugas-rank').value = preset.specific_data.assigned_rank || '';
                    document.getElementById('tugas-position').value = preset.specific_data.assigned_position || '';
                    document.getElementById('tugas-date').value = preset.specific_data.task_date || '';
                    document.getElementById('tugas-location').value = preset.specific_data.task_location || '';
                }
            }

            // Notification
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Contoh Berhasil Dimuat!',
                    text: 'Formulir telah diisi dengan contoh format standar ' + (preset.title || currentType) + '. Anda dapat menyesuaikannya.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    </script>
@endpush
