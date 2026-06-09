<?php

namespace App\Repositories;

use App\Models\Mahasiswa;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MahasiswaRepository
{
    public function paginate(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return Mahasiswa::query()
            ->when($search, function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested->where('nim', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%")
                        ->orWhere('status_kerja', 'like', "%{$search}%")
                        ->orWhere('status_kelulusan', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function allTrainingData(): Collection
    {
        return Mahasiswa::query()
            ->select([
                'ipk',
                'kehadiran',
                'sks_lulus',
                'status_kerja',
                'jenis_kelamin',
                'status_kelulusan',
            ])
            ->get();
    }

    public function allStudentsForDropdown(): Collection
    {
        return Mahasiswa::query()
            ->orderBy('nama')
            ->get(['id', 'nim', 'nama', 'ipk', 'kehadiran', 'sks_lulus', 'status_kerja', 'jenis_kelamin']);
    }

    public function create(array $data): Mahasiswa
    {
        return Mahasiswa::create($data);
    }

    public function update(Mahasiswa $mahasiswa, array $data): Mahasiswa
    {
        $mahasiswa->update($data);

        return $mahasiswa;
    }

    public function delete(Mahasiswa $mahasiswa): void
    {
        $mahasiswa->delete();
    }
}
