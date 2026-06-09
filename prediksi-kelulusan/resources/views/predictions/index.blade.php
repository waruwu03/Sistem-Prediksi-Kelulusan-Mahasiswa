@extends('layouts.app')

@section('title', 'Prediksi Kelulusan')

@section('content')
<div class="row g-3">
    <div class="col-lg-5">
        <div class="panel">
            <h2 class="h5 fw-bold mb-3">Form Data Testing</h2>
            
            <div class="mb-3 position-relative" id="student_search_container">
                <label class="form-label fw-bold text-primary" for="select_student">Auto-Fill dari Data Mahasiswa (Cari NIM / Nama)</label>
                <div class="input-group">
                    <span class="input-group-text"><i data-lucide="search" style="width: 16px; height: 16px;"></i></span>
                    <input class="form-control border-primary" id="select_student" placeholder="Ketik NIM atau nama mahasiswa..." autocomplete="off">
                </div>
                <div id="student_dropdown_list" class="dropdown-menu w-100 shadow-sm" style="display: none; max-height: 250px; overflow-y: auto; position: absolute; z-index: 1000; top: 100%;">
                    @foreach ($students as $student)
                        <button type="button" class="dropdown-item student-option text-start py-2" 
                            data-nim="{{ $student->nim }}"
                            data-nama="{{ $student->nama }}"
                            data-ipk="{{ $student->ipk }}" 
                            data-kehadiran="{{ $student->kehadiran }}" 
                            data-sks_lulus="{{ $student->sks_lulus }}" 
                            data-status_kerja="{{ $student->status_kerja }}" 
                            data-jenis_kelamin="{{ $student->jenis_kelamin }}">
                            <span class="fw-semibold text-primary">{{ $student->nim }}</span> - <span>{{ $student->nama }}</span>
                        </button>
                    @endforeach
                    <div id="no_student_found" class="dropdown-item text-muted disabled text-center py-2" style="display: none;">
                        Mahasiswa tidak ditemukan
                    </div>
                </div>
                <div class="form-text text-muted">Ketik nama atau NIM mahasiswa dari database untuk mengisi form secara otomatis.</div>
            </div>
            
            <hr class="my-3">

            <form method="post" action="{{ route('predictions.predict') }}">
                @csrf
                <input type="hidden" id="input_nim" name="nim" value="{{ old('nim', $input['nim'] ?? '') }}">
                <input type="hidden" id="input_nama_mahasiswa" name="nama_mahasiswa" value="{{ old('nama_mahasiswa', $input['nama_mahasiswa'] ?? '') }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="ipk">IPK</label>
                        <input class="form-control" id="ipk" name="ipk" type="number" step="0.01" min="0" max="4" value="{{ old('ipk', $input['ipk'] ?? '3.50') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="kehadiran">Kehadiran (%)</label>
                        <input class="form-control" id="kehadiran" name="kehadiran" type="number" min="0" max="100" value="{{ old('kehadiran', $input['kehadiran'] ?? '90') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="sks_lulus">SKS Lulus</label>
                        <input class="form-control" id="sks_lulus" name="sks_lulus" type="number" min="0" max="180" value="{{ old('sks_lulus', $input['sks_lulus'] ?? '120') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="status_kerja">Status Kerja</label>
                        <select class="form-select" id="status_kerja" name="status_kerja" required>
                            @foreach (['Tidak Bekerja', 'Part Time', 'Full Time'] as $status)
                                <option value="{{ $status }}" @selected(old('status_kerja', $input['status_kerja'] ?? 'Tidak Bekerja') === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
                        <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                            @foreach (['Laki-laki', 'Perempuan'] as $gender)
                                <option value="{{ $gender }}" @selected(old('jenis_kelamin', $input['jenis_kelamin'] ?? 'Laki-laki') === $gender)>{{ $gender }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="algorithm">Algoritma</label>
                        <select class="form-select" id="algorithm" name="algorithm" required>
                            @foreach ($algorithmOptions as $key => $name)
                                <option value="{{ $key }}" @selected(old('algorithm', $input['algorithm'] ?? 'naive_bayes') === $key)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <button class="btn btn-primary" type="submit">
                        <i data-lucide="sparkles"></i> Prediksi Kelulusan
                    </button>
                    <button class="btn btn-outline-primary" type="submit" formaction="{{ route('predictions.compare') }}">
                        <i data-lucide="git-compare"></i> Bandingkan Semua Algoritma
                    </button>
                    <a class="btn btn-outline-secondary" href="{{ route('predictions.index') }}">
                        <i data-lucide="rotate-ccw"></i> Reset Form
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="panel h-100">
            <h2 class="h5 fw-bold mb-3">Hasil Klasifikasi</h2>
            @isset($result)
                @if(!empty($input['nim']) || !empty($input['nama_mahasiswa']))
                    <div class="d-flex align-items-center gap-2 mb-3 p-2 rounded-3" style="background: var(--app-bg); border: 1px solid var(--app-border);">
                        <i data-lucide="user-circle" style="width:20px;height:20px;color:var(--app-primary);"></i>
                        <div>
                            <div class="fw-bold small">{{ $input['nama_mahasiswa'] ?? '-' }}</div>
                            <div class="muted" style="font-size:.78rem;">NIM: {{ $input['nim'] ?? '-' }}</div>
                        </div>
                    </div>
                @endif
                <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                    <span class="status-pill {{ $result['status'] === 'Lulus' ? 'text-bg-success' : 'text-bg-danger' }}">
                        {{ $result['display_status'] }}
                    </span>
                    <span class="badge text-bg-light border">{{ $result['algorithm_name'] }}</span>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <div class="border rounded-3 p-3">
                            <div class="muted small">Probabilitas Lulus</div>
                            <div class="fs-3 fw-bold text-success">{{ number_format($result['probability_lulus'], 2) }}%</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="border rounded-3 p-3">
                            <div class="muted small">Probabilitas Tidak Lulus</div>
                            <div class="fs-3 fw-bold text-danger">{{ number_format($result['probability_tidak_lulus'], 2) }}%</div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-semibold">Tingkat Keyakinan Model</span>
                        <span>{{ number_format($result['confidence'], 2) }}%</span>
                    </div>
                    <div class="progress" style="height: 14px;">
                        <div class="progress-bar" style="width: {{ $result['confidence'] }}%"></div>
                    </div>
                </div>
                <p class="mb-0">{{ $result['keterangan'] }}</p>
            @else
                <div class="text-center muted py-5">Belum ada hasil prediksi.</div>
            @endisset
        </div>
    </div>
</div>

@isset($allResults)
    <div class="table-panel mt-3">
        <h2 class="h5 fw-bold mb-3">Hasil Perbandingan Semua Algoritma</h2>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>Algoritma</th>
                    <th>Status</th>
                    <th>Probabilitas Lulus</th>
                    <th>Keyakinan</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($allResults as $row)
                    <tr>
                        <td>{{ $row['algorithm_name'] }}</td>
                        <td>{{ $row['display_status'] }}</td>
                        <td>{{ number_format($row['probability_lulus'], 2) }}%</td>
                        <td>{{ number_format($row['confidence'], 2) }}%</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endisset

<div class="row g-3 mt-1">
    <div class="col-lg-5">
        <div class="table-panel">
            <h2 class="h5 fw-bold mb-3">Perbandingan Algoritma</h2>
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>Algoritma</th>
                    <th>Akurasi</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($comparison as $row)
                    <tr>
                        <td>{{ $row['algorithm'] }}</td>
                        <td>{{ number_format($row['accuracy'], 2) }}%</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="table-panel">
            <h2 class="h5 fw-bold mb-3">Riwayat Prediksi</h2>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th>Mahasiswa</th>
                        <th>Algoritma</th>
                        <th>Status</th>
                        <th>Lulus</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($histories as $history)
                        <tr>
                            <td>
                                @if($history->nama_mahasiswa)
                                    <div class="fw-semibold small">{{ $history->nama_mahasiswa }}</div>
                                    <div class="muted" style="font-size:.75rem;">{{ $history->nim }}</div>
                                @else
                                    <span class="muted small">-</span>
                                @endif
                            </td>
                            <td>{{ $history->algorithm }}</td>
                            <td>{{ $history->predicted_status }}</td>
                            <td>{{ number_format($history->probability_lulus, 2) }}%</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('predictions.print', $history) }}" target="_blank">
                                    <i data-lucide="file-down"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center muted py-4">Belum ada riwayat.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $histories->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectStudent = document.getElementById('select_student');
    const studentDropdownList = document.getElementById('student_dropdown_list');
    const noStudentFound = document.getElementById('no_student_found');
    const studentOptions = document.querySelectorAll('.student-option');
    
    const inputIpk = document.getElementById('ipk');
    const inputKehadiran = document.getElementById('kehadiran');
    const inputSksLulus = document.getElementById('sks_lulus');
    const selectStatusKerja = document.getElementById('status_kerja');
    const selectJenisKelamin = document.getElementById('jenis_kelamin');

    // Show dropdown on focus
    selectStudent.addEventListener('focus', function () {
        studentDropdownList.style.display = 'block';
        filterOptions();
    });

    // Hide dropdown on click outside
    document.addEventListener('click', function (e) {
        if (!document.getElementById('student_search_container').contains(e.target)) {
            studentDropdownList.style.display = 'none';
        }
    });

    // Filter dropdown options based on input
    selectStudent.addEventListener('input', function () {
        studentDropdownList.style.display = 'block';
        filterOptions();
    });

    function filterOptions() {
        const query = selectStudent.value.toLowerCase().trim();
        let foundAny = false;

        studentOptions.forEach(function (option) {
            const nim = option.getAttribute('data-nim').toLowerCase();
            const nama = option.getAttribute('data-nama').toLowerCase();

            if (nim.includes(query) || nama.includes(query)) {
                option.style.display = 'block';
                foundAny = true;
            } else {
                option.style.display = 'none';
            }
        });

        if (foundAny) {
            noStudentFound.style.display = 'none';
        } else {
            noStudentFound.style.display = 'block';
        }
    }

    // Auto-fill form when option is clicked
    studentOptions.forEach(function (option) {
        option.addEventListener('click', function () {
            const nim = this.getAttribute('data-nim');
            const nama = this.getAttribute('data-nama');
            const ipk = this.getAttribute('data-ipk');
            const kehadiran = this.getAttribute('data-kehadiran');
            const sks_lulus = this.getAttribute('data-sks_lulus');
            const status_kerja = this.getAttribute('data-status_kerja');
            const jenis_kelamin = this.getAttribute('data-jenis_kelamin');

            selectStudent.value = nim + ' - ' + nama;
            document.getElementById('input_nim').value = nim;
            document.getElementById('input_nama_mahasiswa').value = nama;
            inputIpk.value = ipk;
            inputKehadiran.value = kehadiran;
            inputSksLulus.value = sks_lulus;
            selectStatusKerja.value = status_kerja;
            selectJenisKelamin.value = jenis_kelamin;

            studentDropdownList.style.display = 'none';
        });
    });
});
</script>
@endpush
