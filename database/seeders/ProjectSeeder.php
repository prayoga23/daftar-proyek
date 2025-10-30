<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Services\DataNormalizationService;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $normalizationService = new DataNormalizationService();
        $sampleData = $normalizationService->createSampleData();

        // Hapus data lama jika ada
        Project::truncate();

        // Insert data sample
        foreach ($sampleData as $data) {
            Project::create($data);
        }

        // Tambahkan data sesuai dengan gambar (format newline)
        Project::create([
            'nama_proyek' => 'slf bhumyamca',
            'owner' => 'mas adi',
            'jenis_pekerjaan' => 'slf',
            'yang_mengerjakan' => 'mm',
            'status_pekerjaan' => 'sisa 15'
        ]);

        Project::create([
            'nama_proyek' => 'Kantor yayasan & tmpt ibadah jl. padang no 28',
            'owner' => 'pak danang',
            'jenis_pekerjaan' => 'pbg',
            'yang_mengerjakan' => 'pak danang',
            'status_pekerjaan' => 'baru banner yang dikirim'
        ]);

        Project::create([
            'nama_proyek' => 'GAMBAR rmh pak agung PJR',
            'owner' => 'p. agung',
            'jenis_pekerjaan' => 'ars',
            'yang_mengerjakan' => 'mame',
            'status_pekerjaan' => 'file di folder pjr'
        ]);

        Project::create([
            'nama_proyek' => 'gmbr restoran simatupang',
            'owner' => 'pak miko',
            'jenis_pekerjaan' => 'ars',
            'yang_mengerjakan' => 'harto',
            'status_pekerjaan' => 'sudah selesai.'
        ]);

        // Data untuk r. tinggal jl. teuku umar no.43 - dinormalisasi menjadi multiple records
        Project::create([
            'nama_proyek' => 'r. tinggal jl. teuku umar no.43',
            'owner' => 'pak agung citata mtg',
            'jenis_pekerjaan' => 'irk',
            'yang_mengerjakan' => 'ptsp',
            'status_pekerjaan' => 'selesai. dibantu ucup, mame blm byr'
        ]);
        
        Project::create([
            'nama_proyek' => 'r. tinggal jl. teuku umar no.43',
            'owner' => 'pak agung citata mtg',
            'jenis_pekerjaan' => 'cagar budaya',
            'yang_mengerjakan' => 'pak danang',
            'status_pekerjaan' => 'sudah selesai sidang'
        ]);
        
        Project::create([
            'nama_proyek' => 'r. tinggal jl. teuku umar no.43',
            'owner' => 'pak agung citata mtg',
            'jenis_pekerjaan' => 'pbg',
            'yang_mengerjakan' => 'ptsp',
            'status_pekerjaan' => 'Menunggu Dokumen SKRD dari PTSP.'
        ]);

        // Data untuk r. tinggal jl. mataram gol. b - dinormalisasi
        Project::create([
            'nama_proyek' => 'r. tinggal jl. mataram gol. b',
            'owner' => 'p. bungaran matondang',
            'jenis_pekerjaan' => 'irk',
            'yang_mengerjakan' => 'jambi / tika',
            'status_pekerjaan' => 'proses irk citata'
        ]);
        
        Project::create([
            'nama_proyek' => 'r. tinggal jl. mataram gol. b',
            'owner' => 'p. valentino',
            'jenis_pekerjaan' => 'cagar budaya',
            'yang_mengerjakan' => 'renc. pak budi heri',
            'status_pekerjaan' => 'sedang proses sidang TAP Pak Budi Heri.'
        ]);

        // Data untuk input pbg hotel jl.subuh - dinormalisasi
        Project::create([
            'nama_proyek' => 'input pbg hotel jl.subuh',
            'owner' => 'pak ben',
            'jenis_pekerjaan' => 'gmbr arsitek',
            'yang_mengerjakan' => 'pak ben',
            'status_pekerjaan' => 'sudah di kita'
        ]);
        
        Project::create([
            'nama_proyek' => 'input pbg hotel jl.subuh',
            'owner' => 'pak ben',
            'jenis_pekerjaan' => 'struktur',
            'yang_mengerjakan' => 'pak ben',
            'status_pekerjaan' => 'sudah di kita'
        ]);
        
        Project::create([
            'nama_proyek' => 'input pbg hotel jl.subuh',
            'owner' => 'pak ben',
            'jenis_pekerjaan' => 'mep',
            'yang_mengerjakan' => 'pak ben',
            'status_pekerjaan' => 'blm di kirim dari pak ben'
        ]);
        
        Project::create([
            'nama_proyek' => 'input pbg hotel jl.subuh',
            'owner' => 'pak ben',
            'jenis_pekerjaan' => 'ska arsitek',
            'yang_mengerjakan' => 'pak ben',
            'status_pekerjaan' => 'udh ada dari pak ben'
        ]);
        
        Project::create([
            'nama_proyek' => 'input pbg hotel jl.subuh',
            'owner' => 'pak ben',
            'jenis_pekerjaan' => 'ska str',
            'yang_mengerjakan' => '?',
            'status_pekerjaan' => 'renc. dari kita'
        ]);
        
        Project::create([
            'nama_proyek' => 'input pbg hotel jl.subuh',
            'owner' => 'pak ben',
            'jenis_pekerjaan' => 'ska mep',
            'yang_mengerjakan' => '?',
            'status_pekerjaan' => 'blm jelas mau dari siapa'
        ]);

        $this->command->info('Sample projects created successfully!');
        $this->command->info('Total projects: ' . Project::count());
        
        // Tampilkan info normalisasi
        $projects = Project::all();
        $totalNormalizedRows = 0;
        
        foreach ($projects as $project) {
            if ($normalizationService->needsNormalization($project)) {
                $rowCount = $normalizationService->getNormalizedRowCount($project);
                $totalNormalizedRows += $rowCount;
                $this->command->info("Project '{$project->nama_proyek}' will be normalized to {$rowCount} rows");
            }
        }
        
        $this->command->info("Total rows after normalization: {$totalNormalizedRows}");
    }
}
