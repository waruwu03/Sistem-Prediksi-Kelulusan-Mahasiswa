<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        $firstNames = [
            'Andi', 'Budi', 'Citra', 'Dewi', 'Eka', 'Fajar', 'Gita', 'Hadi', 'Intan', 'Joko',
            'Kartika', 'Lina', 'Maya', 'Nanda', 'Putri', 'Rizky', 'Sari', 'Tono', 'Vina', 'Yoga',
            'Adi', 'Agus', 'Anwar', 'Bagus', 'Dadan', 'Dedi', 'Dian', 'Ferry', 'Hendra', 'Iwan',
            'Rian', 'Yudi', 'Zaki', 'Amalia', 'Bella', 'Dina', 'Fitri', 'Indah', 'Mega', 'Rina'
        ];
        $lastNames = [
            'Pratama', 'Saputra', 'Wulandari', 'Santoso', 'Permata', 'Wijaya', 'Nugraha',
            'Lestari', 'Rahman', 'Kusuma', 'Maulana', 'Handayani', 'Firmansyah', 'Utami',
            'Siregar', 'Ginting', 'Sitorus', 'Nasution', 'Lubis', 'Sinaga', 'Simanjuntak'
        ];
        $workStatuses = ['Tidak Bekerja', 'Part Time', 'Full Time'];
        $genders = ['Laki-laki', 'Perempuan'];

        $records = [];
        $now = now();
        $generatedNames = [];

        for ($i = 1; $i <= 500; $i++) {
            $ipk = round(mt_rand(190, 400) / 100, 2);
            $kehadiran = mt_rand(45, 100);
            $sksLulus = mt_rand(60, 150);
            $statusKerja = $workStatuses[array_rand($workStatuses)];
            $jenisKelamin = $genders[array_rand($genders)];

            $workPenalty = match ($statusKerja) {
                'Full Time' => 0.65,
                'Part Time' => 0.25,
                default => 0,
            };

            $score = ($ipk * 22) + ($kehadiran * 0.35) + ($sksLulus * 0.22) - ($workPenalty * 10);
            $statusKelulusan = $score >= 92 ? 'Lulus' : 'Tidak Lulus';

            if ($ipk >= 3.35 && $kehadiran >= 82 && $sksLulus >= 118) {
                $statusKelulusan = 'Lulus';
            }

            if ($ipk < 2.35 || $kehadiran < 60 || $sksLulus < 80) {
                $statusKelulusan = 'Tidak Lulus';
            }

            do {
                $nama = $firstNames[array_rand($firstNames)].' '.$lastNames[array_rand($lastNames)];
            } while (in_array($nama, $generatedNames, true));
            $generatedNames[] = $nama;

            $records[] = [
                'nim' => '2301'.str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                'nama' => $nama,
                'ipk' => $ipk,
                'kehadiran' => $kehadiran,
                'sks_lulus' => $sksLulus,
                'status_kerja' => $statusKerja,
                'jenis_kelamin' => $jenisKelamin,
                'status_kelulusan' => $statusKelulusan,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::transaction(function () use ($records): void {
            foreach (array_chunk($records, 100) as $chunk) {
                Mahasiswa::upsert(
                    $chunk,
                    ['nim'],
                    ['nama', 'ipk', 'kehadiran', 'sks_lulus', 'status_kerja', 'jenis_kelamin', 'status_kelulusan', 'updated_at'],
                );
            }
        });
    }
}
