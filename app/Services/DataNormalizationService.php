<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Collection;

class DataNormalizationService
{
    /**
     * Normalisasi data proyek yang memiliki multiple values
     */
    public function normalizeProjectData(Project $project): Collection
    {
        // Split data berdasarkan koma
        $owners = $this->splitData($project->owner);
        $jenisPekerjaans = $this->splitData($project->jenis_pekerjaan);
        $yangMengerjakans = $this->splitData($project->yang_mengerjakan);
        $statusPekerjaans = $this->splitData($project->status_pekerjaan);

        // Tentukan jumlah maksimal entries
        $maxEntries = max(
            count($owners),
            count($jenisPekerjaans),
            count($yangMengerjakans),
            count($statusPekerjaans)
        );

        $normalizedData = collect();

        for ($i = 0; $i < $maxEntries; $i++) {
            $normalizedData->push([
                'id' => $project->id,
                'nama_proyek' => $project->nama_proyek,
                'owner' => $owners[$i] ?? $owners[0] ?? '',
                'jenis_pekerjaan' => $jenisPekerjaans[$i] ?? '',
                'yang_mengerjakan' => $yangMengerjakans[$i] ?? '',
                'status_pekerjaan' => $statusPekerjaans[$i] ?? '',
                'is_first_row' => $i === 0, // Flag untuk baris pertama
                'row_index' => $i
            ]);
        }

        return $normalizedData;
    }

    /**
     * Normalisasi semua data proyek
     */
    public function normalizeAllProjects(): Collection
    {
        $projects = Project::all();
        $allNormalizedData = collect();

        foreach ($projects as $project) {
            $normalizedData = $this->normalizeProjectData($project);
            $allNormalizedData = $allNormalizedData->merge($normalizedData);
        }

        return $allNormalizedData;
    }

    /**
     * Split data berdasarkan koma dan bersihkan whitespace
     */
    private function splitData(?string $data): array
    {
        if (empty($data)) {
            return [''];
        }

        return array_filter(
            array_map('trim', explode(',', $data)),
            function ($item) {
                return !empty($item);
            }
        );
    }

    /**
     * Cek apakah data perlu dinormalisasi
     */
    public function needsNormalization(Project $project): bool
    {
        $owners = $this->splitData($project->owner);
        $jenisPekerjaans = $this->splitData($project->jenis_pekerjaan);
        $yangMengerjakans = $this->splitData($project->yang_mengerjakan);
        $statusPekerjaans = $this->splitData($project->status_pekerjaan);

        return max(
            count($owners),
            count($jenisPekerjaans),
            count($yangMengerjakans),
            count($statusPekerjaans)
        ) > 1;
    }

    /**
     * Hitung jumlah baris setelah normalisasi
     */
    public function getNormalizedRowCount(Project $project): int
    {
        $owners = $this->splitData($project->owner);
        $jenisPekerjaans = $this->splitData($project->jenis_pekerjaan);
        $yangMengerjakans = $this->splitData($project->yang_mengerjakan);
        $statusPekerjaans = $this->splitData($project->status_pekerjaan);

        return max(
            count($owners),
            count($jenisPekerjaans),
            count($yangMengerjakans),
            count($statusPekerjaans)
        );
    }

    /**
     * Buat data sample untuk testing
     */
    public function createSampleData(): array
    {
        return [
            [
                'nama_proyek' => 'Gedung Perkantoran Sudirman Tower',
                'owner' => 'PT. Konstruksi Maju, PT. Bangun Jaya',
                'jenis_pekerjaan' => 'Struktur Beton, Instalasi Listrik, Plumbing System',
                'yang_mengerjakan' => 'Tim Struktur, Tim Elektrik, Tim Plumbing',
                'status_pekerjaan' => 'Selesai 80%, Progress 60%, Belum Dimulai'
            ],
            [
                'nama_proyek' => 'Rumah Sakit Umum Daerah Jakarta',
                'owner' => 'Dinas Kesehatan DKI',
                'jenis_pekerjaan' => 'Renovasi Bangunan, Instalasi Medis, Landscaping',
                'yang_mengerjakan' => 'Kontraktor Utama, Tim Medis, Tim Taman',
                'status_pekerjaan' => 'Sudah Selesai, Progress 40%, Belum Dimulai'
            ],
            [
                'nama_proyek' => 'Mall Shopping Center Bekasi',
                'owner' => 'CV. Properti Sukses, PT. Arsitek Modern, PT. Interior Design',
                'jenis_pekerjaan' => 'Design Interior, Struktur Baja, AC Central, Fire Safety, Parking System, Security System',
                'yang_mengerjakan' => 'Designer, Tim Baja, Tim AC, Tim Safety, Tim Parkir, Tim Security',
                'status_pekerjaan' => 'Design Selesai, Progress 70%, Belum Dimulai, Progress 30%, Belum Dimulai, Progress 20%'
            ]
        ];
    }
}
