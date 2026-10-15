@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('menu.template.menu'), __('menu.template.list')]">
        <a href="{{ route('template.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> {{ __('menu.general.create') }}
        </a>
    </x-breadcrumb>

    <!-- Quick Template Cards for the 5 types -->
    <div class="row g-3 mb-4">
        @php
            $templateCards = [
                [
                    'type' => 'permohonan',
                    'title' => 'Surat Permohonan',
                    'desc' => 'Permohonan izin, bantuan sarana/dana, narasumber, dll.',
                    'icon' => 'bx-send',
                    'color' => 'primary',
                ],
                [
                    'type' => 'pemberitahuan',
                    'title' => 'Surat Pemberitahuan',
                    'desc' => 'Pengumuman kedinasan, penyesuaian jadwal, informasi publik.',
                    'icon' => 'bx-bell',
                    'color' => 'warning',
                ],
                [
                    'type' => 'undangan',
                    'title' => 'Surat Undangan',
                    'desc' => 'Undangan rapat koordinasi, seminar, pertemuan resmi.',
                    'icon' => 'bx-calendar-event',
                    'color' => 'success',
                ],
                [
                    'type' => 'balasan',
                    'title' => 'Surat Balasan',
                    'desc' => 'Respon surat masuk, persetujuan kerja sama, magang.',
                    'icon' => 'bx-reply',
                    'color' => 'info',
                ],
                [
                    'type' => 'tugas',
                    'title' => 'Surat Tugas',
                    'desc' => 'Penugasan resmi kedinasan, pelatihan, dinas luar kota.',
                    'icon' => 'bx-briefcase-alt-2',
                    'color' => 'danger',
                ],
            ];
        @endphp

        @foreach($templateCards as $card)
            <div class="col-sm-6 col-md-4 col-xl">
                <div class="card h-100 shadow-sm border border-{{ $card['color'] }} border-opacity-25 hover-shadow transition">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-label-{{ $card['color'] }} p-2 rounded me-2">
                                    <i class="bx {{ $card['icon'] }} fs-4"></i>
                                </span>
                                <h6 class="mb-0 fw-bold">{{ $card['title'] }}</h6>
                            </div>
                            <p class="small text-muted mb-2 lh-sm">{{ $card['desc'] }}</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                            <span class="badge bg-{{ $card['color'] }} rounded-pill">{{ $counts[$card['type']] ?? 0 }} Dokumen</span>
                            <a href="{{ route('template.create', ['type' => $card['type']]) }}" class="btn btn-xs btn-outline-{{ $card['color'] }}">
                                <i class="bx bx-plus"></i> Buat
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header pb-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="fw-semibold">Filter Jenis:</span>
                <a href="{{ route('template.index', ['search' => $search]) }}"
                   class="btn btn-sm {{ empty($selectedType) ? 'btn-primary' : 'btn-outline-secondary' }}">
                    Semua ({{ $counts['all'] ?? 0 }})
                </a>
                @foreach($types as $typeEnum)
                    <a href="{{ route('template.index', ['type' => $typeEnum->value, 'search' => $search]) }}"
                       class="btn btn-sm {{ $selectedType === $typeEnum->value ? 'btn-' . $typeEnum->badgeColor() : 'btn-outline-secondary' }}">
                        {{ $typeEnum->label() }} ({{ $counts[$typeEnum->value] ?? 0 }})
                    </a>
                @endforeach
            </div>

            <!-- Search Form -->
            <form action="{{ route('template.index') }}" method="GET" class="d-flex align-items-center">
                @if($selectedType)
                    <input type="hidden" name="type" value="{{ $selectedType }}">
                @endif
                <div class="input-group input-group-merge input-group-sm">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari template..." value="{{ $search }}">
                    @if($search)
                        <a href="{{ route('template.index', ['type' => $selectedType]) }}" class="btn btn-outline-secondary">
                            <i class="bx bx-x"></i>
                        </a>
                    @endif
                    <button class="btn btn-primary" type="submit">{{ __('menu.general.filter') }}</button>
                </div>
            </form>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-light">
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Jenis & Judul Template</th>
                    <th>Nomor & Perihal</th>
                    <th>Tujuan Surat</th>
                    <th>Tanggal</th>
                    <th class="text-center" style="width: 220px;">Aksi</th>
                </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                @forelse($data as $template)
                    <tr>
                        <td>{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}</td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="badge bg-label-{{ $template->type_badge }} mb-1 align-self-start">
                                    <i class="bx {{ $template->type_enum?->icon() }} me-1"></i>
                                    {{ $template->type_label }}
                                </span>
                                <a href="{{ route('template.show', $template) }}" class="fw-bold text-dark text-wrap" style="max-width: 280px;">
                                    {{ $template->title }}
                                </a>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column text-wrap" style="max-width: 260px;">
                                <span class="text-muted small">No: <strong class="text-dark">{{ $template->reference_number ?: '-' }}</strong></span>
                                <span class="small text-truncate" title="{{ $template->regarding ?: '-' }}">Hal: {{ $template->regarding ?: '-' }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column text-wrap" style="max-width: 200px;">
                                <span class="fw-semibold">{{ $template->to_name ?: '-' }}</span>
                                <span class="text-muted small text-truncate">{{ $template->to_address ?: '-' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="small">{{ $template->formatted_letter_date }}</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <!-- Download Word Button -->
                                <a href="{{ route('template.download', $template) }}"
                                   class="btn btn-sm btn-info text-white"
                                   title="Unduh Microsoft Word (.docx)"
                                   data-bs-toggle="tooltip">
                                    <i class="bx bxs-file-doc me-1"></i> Word
                                </a>

                                <!-- Preview Button -->
                                <a href="{{ route('template.show', $template) }}"
                                   class="btn btn-sm btn-outline-secondary"
                                   title="Lihat Pratinjau Surat"
                                   data-bs-toggle="tooltip">
                                    <i class="bx bx-show"></i>
                                </a>

                                <!-- Edit Button -->
                                <a href="{{ route('template.edit', $template) }}"
                                   class="btn btn-sm btn-outline-warning"
                                   title="Edit Template"
                                   data-bs-toggle="tooltip">
                                    <i class="bx bx-edit-alt"></i>
                                </a>

                                <!-- More actions dropdown -->
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <form action="{{ route('template.duplicate', $template) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bx bx-copy me-1"></i> Duplikat Template
                                                </button>
                                            </form>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('template.destroy', $template) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="dropdown-item text-danger btn-delete">
                                                    <i class="bx bx-trash me-1"></i> {{ __('menu.general.delete') }}
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="my-3">
                                <i class="bx bx-file-blank display-3 text-muted"></i>
                                <h5 class="mt-2">{{ __('menu.general.empty') }}</h5>
                                <p class="text-muted">Belum ada template surat yang dibuat. Klik tombol di bawah untuk membuat template baru.</p>
                                <a href="{{ route('template.create') }}" class="btn btn-primary">
                                    <i class="bx bx-plus me-1"></i> Buat Template Sekarang
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($data->hasPages())
            <div class="card-footer py-2">
                {{ $data->links() }}
            </div>
        @endif
    </div>
@endsection
