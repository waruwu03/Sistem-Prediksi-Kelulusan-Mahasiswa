@extends('layouts.app')

@section('title', $mahasiswa->exists ? 'Edit Mahasiswa' : 'Tambah Mahasiswa')

@section('content')
<div class="panel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h5 fw-bold mb-1">{{ $mahasiswa->exists ? 'Edit Data Mahasiswa' : 'Tambah Data Mahasiswa' }}</h2>
            <div class="muted">Lengkapi data akademik mahasiswa</div>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('mahasiswa.index') }}">
            <i data-lucide="arrow-left"></i> Kembali
        </a>
    </div>

    <form method="post" action="{{ $mahasiswa->exists ? route('mahasiswa.update', $mahasiswa) : route('mahasiswa.store') }}">
        @csrf
        @if ($mahasiswa->exists)
            @method('put')
        @endif

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="nim">NIM</label>
                <input class="form-control" id="nim" name="nim" value="{{ old('nim', $mahasiswa->nim) }}" required>
            </div>
            <div class="col-md-8">
                <label class="form-label" for="nama">Nama</label>
                <input class="form-control" id="nama" name="nama" value="{{ old('nama', $mahasiswa->nama) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="ipk">IPK</label>
                <input class="form-control" id="ipk" name="ipk" type="number" min="0" max="4" step="0.01" value="{{ old('ipk', $mahasiswa->ipk) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="kehadiran">Kehadiran (%)</label>
                <input class="form-control" id="kehadiran" name="kehadiran" type="number" min="0" max="100" value="{{ old('kehadiran', $mahasiswa->kehadiran) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="sks_lulus">SKS Lulus</label>
                <input class="form-control" id="sks_lulus" name="sks_lulus" type="number" min="0" max="180" value="{{ old('sks_lulus', $mahasiswa->sks_lulus) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="status_kerja">Status Kerja</label>
                <select class="form-select" id="status_kerja" name="status_kerja" required>
                    @foreach (['Tidak Bekerja', 'Part Time', 'Full Time'] as $status)
                        <option value="{{ $status }}" @selected(old('status_kerja', $mahasiswa->status_kerja ?: 'Tidak Bekerja') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
                <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                    @foreach (['Laki-laki', 'Perempuan'] as $gender)
                        <option value="{{ $gender }}" @selected(old('jenis_kelamin', $mahasiswa->jenis_kelamin ?: 'Laki-laki') === $gender)>{{ $gender }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="status_kelulusan">Status Kelulusan</label>
                <select class="form-select" id="status_kelulusan" name="status_kelulusan" required>
                    @foreach (['Lulus', 'Tidak Lulus'] as $status)
                        <option value="{{ $status }}" @selected(old('status_kelulusan', $mahasiswa->status_kelulusan ?: 'Lulus') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button class="btn btn-primary" type="submit">
                <i data-lucide="save"></i> Simpan
            </button>
            <a class="btn btn-outline-secondary" href="{{ route('mahasiswa.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
