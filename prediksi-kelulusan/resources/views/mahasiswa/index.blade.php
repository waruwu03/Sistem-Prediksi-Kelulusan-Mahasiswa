@extends('layouts.app')

@section('title', 'Data Mahasiswa')

@section('content')
<div class="table-panel">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
        <div>
            <h2 class="h5 fw-bold mb-1">Data Mahasiswa</h2>
            <div class="muted">NIM, akademik, dan status kelulusan</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-primary" href="{{ route('mahasiswa.create') }}">
                <i data-lucide="plus"></i> Tambah Data
            </a>
            <a class="btn btn-outline-success" href="{{ route('mahasiswa.export') }}">
                <i data-lucide="download"></i> Export Excel
            </a>
        </div>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-lg-7">
            <form class="d-flex gap-2" method="get" action="{{ route('mahasiswa.index') }}">
                <input class="form-control" name="search" value="{{ $search }}" placeholder="Cari NIM, nama, status kerja, atau kelulusan">
                <button class="btn btn-outline-primary icon-btn" type="submit" title="Cari"><i data-lucide="search"></i></button>
            </form>
        </div>
        <div class="col-lg-5">
            <form class="d-flex gap-2" method="post" action="{{ route('mahasiswa.import') }}" enctype="multipart/form-data">
                @csrf
                <input class="form-control" name="file" type="file" accept=".csv,.txt" required>
                <button class="btn btn-outline-secondary" type="submit">
                    <i data-lucide="upload"></i> Import Excel
                </button>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
            <tr>
                <th>NIM</th>
                <th>Nama</th>
                <th>IPK</th>
                <th>Kehadiran</th>
                <th>SKS</th>
                <th>Status Kerja</th>
                <th>Jenis Kelamin</th>
                <th>Kelulusan</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse ($mahasiswa as $row)
                <tr>
                    <td class="fw-semibold">{{ $row->nim }}</td>
                    <td>{{ $row->nama }}</td>
                    <td>{{ number_format($row->ipk, 2) }}</td>
                    <td>{{ $row->kehadiran }}%</td>
                    <td>{{ $row->sks_lulus }}</td>
                    <td>{{ $row->status_kerja }}</td>
                    <td>{{ $row->jenis_kelamin }}</td>
                    <td>
                        <span class="status-pill {{ $row->status_kelulusan === 'Lulus' ? 'text-bg-success' : 'text-bg-danger' }}">
                            {{ $row->status_kelulusan }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="btn-group">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('mahasiswa.edit', $row) }}" title="Edit">
                                <i data-lucide="pencil"></i>
                            </a>
                            <form method="post" action="{{ route('mahasiswa.destroy', $row) }}" onsubmit="return confirm('Hapus data mahasiswa ini?')">
                                @csrf
                                @method('delete')
                                <button class="btn btn-sm btn-outline-danger" type="submit" title="Hapus">
                                    <i data-lucide="trash-2"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center muted py-4">Data mahasiswa belum tersedia.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $mahasiswa->links() }}
</div>
@endsection
