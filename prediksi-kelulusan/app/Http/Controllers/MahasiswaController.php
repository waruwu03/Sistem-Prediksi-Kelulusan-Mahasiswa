<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportMahasiswaRequest;
use App\Http\Requests\StoreMahasiswaRequest;
use App\Http\Requests\UpdateMahasiswaRequest;
use App\Models\Mahasiswa;
use App\Repositories\MahasiswaRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MahasiswaController extends Controller
{
    public function __construct(private readonly MahasiswaRepository $mahasiswaRepository)
    {
    }

    public function index(Request $request): View
    {
        return view('mahasiswa.index', [
            'mahasiswa' => $this->mahasiswaRepository->paginate($request->string('search')->toString()),
            'search' => $request->string('search')->toString(),
        ]);
    }

    public function create(): View
    {
        return view('mahasiswa.form', ['mahasiswa' => new Mahasiswa()]);
    }

    public function store(StoreMahasiswaRequest $request): RedirectResponse
    {
        $this->mahasiswaRepository->create($request->validated());

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil ditambahkan.');
    }

    public function edit(Mahasiswa $mahasiswa): View
    {
        return view('mahasiswa.form', compact('mahasiswa'));
    }

    public function update(UpdateMahasiswaRequest $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        $this->mahasiswaRepository->update($mahasiswa, $request->validated());

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroy(Mahasiswa $mahasiswa): RedirectResponse
    {
        $this->mahasiswaRepository->delete($mahasiswa);

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil dihapus.');
    }

    public function import(ImportMahasiswaRequest $request): RedirectResponse
    {
        $file = fopen($request->file('file')->getRealPath(), 'r');
        $header = fgetcsv($file);
        $imported = 0;

        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);

            if (! $data || empty($data['nim'])) {
                continue;
            }

            Mahasiswa::updateOrCreate(
                ['nim' => $data['nim']],
                [
                    'nama' => $data['nama'],
                    'ipk' => (float) $data['ipk'],
                    'kehadiran' => (int) $data['kehadiran'],
                    'sks_lulus' => (int) $data['sks_lulus'],
                    'status_kerja' => $data['status_kerja'],
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'status_kelulusan' => $data['status_kelulusan'],
                ],
            );
            $imported++;
        }

        fclose($file);

        return back()->with('success', "{$imported} data berhasil diimport.");
    }

    public function export(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="data-mahasiswa.csv"',
        ];

        return Response::streamDownload(function (): void {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['nim', 'nama', 'ipk', 'kehadiran', 'sks_lulus', 'status_kerja', 'jenis_kelamin', 'status_kelulusan']);

            Mahasiswa::query()->orderBy('nim')->chunk(100, function ($rows) use ($output): void {
                foreach ($rows as $row) {
                    fputcsv($output, [
                        $row->nim,
                        $row->nama,
                        $row->ipk,
                        $row->kehadiran,
                        $row->sks_lulus,
                        $row->status_kerja,
                        $row->jenis_kelamin,
                        $row->status_kelulusan,
                    ]);
                }
            });

            fclose($output);
        }, 'data-mahasiswa.csv', $headers);
    }
}
