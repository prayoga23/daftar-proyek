<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Services\DataNormalizationService;
use Livewire\Component;

class CreateProject extends Component
{
    public $nama_proyek = '';
    
    // Properties untuk input fields
    public $owner_input = '';
    public $yang_mengerjakan_input = '';
    public $job_desk_input = '';
    public $jenis_pekerjaan_selected = '';
    public $status_jenis_pekerjaan = '';
    public $status_yang_mengerjakan = '';
    public $status_pekerjaan = '';
    
    // Arrays untuk menyimpan data yang dipilih
    public $selected_owners = [];
    public $selected_yang_mengerjakan = [];
    public $selected_jenis_pekerjaan = [];
    public $job_desk_assignments = [];

    // Methods untuk menangani Owner
    public function addOwner()
    {
        if (!empty($this->owner_input)) {
            $this->selected_owners[] = $this->owner_input;
            $this->owner_input = '';
        }
    }

    public function removeOwner($index)
    {
        unset($this->selected_owners[$index]);
        $this->selected_owners = array_values($this->selected_owners);
    }

    // Methods untuk menangani Yang Mengerjakan
    public function addYangMengerjakan()
    {
        if (!empty($this->yang_mengerjakan_input) && !empty($this->job_desk_input)) {
            $this->selected_yang_mengerjakan[] = [
                'nama' => $this->yang_mengerjakan_input,
                'job_desk' => $this->job_desk_input
            ];
            $this->yang_mengerjakan_input = '';
            $this->job_desk_input = '';
        }
    }

    public function removeYangMengerjakan($index)
    {
        unset($this->selected_yang_mengerjakan[$index]);
        $this->selected_yang_mengerjakan = array_values($this->selected_yang_mengerjakan);
    }

    // Methods untuk menangani Jenis Pekerjaan
    public function addJenisPekerjaan()
    {
        if (!empty($this->jenis_pekerjaan_selected)) {
            $this->selected_jenis_pekerjaan[] = $this->jenis_pekerjaan_selected;
            $this->jenis_pekerjaan_selected = '';
        }
    }

    public function removeJenisPekerjaan($index)
    {
        unset($this->selected_jenis_pekerjaan[$index]);
        $this->selected_jenis_pekerjaan = array_values($this->selected_jenis_pekerjaan);
    }

    // Methods untuk menangani Job Desk Assignments
    public function addJobDeskAssignment()
    {
        if (!empty($this->status_yang_mengerjakan) && !empty($this->status_pekerjaan)) {
            // Ekstrak job desk dari yang_mengerjakan untuk menentukan jenis_pekerjaan
            $jenisPekerjaan = $this->getJenisPekerjaanFromYangMengerjakan($this->status_yang_mengerjakan);
            
            $this->job_desk_assignments[] = [
                'jenis_pekerjaan' => $jenisPekerjaan,
                'yang_mengerjakan' => $this->status_yang_mengerjakan,
                'keterangan' => $this->status_pekerjaan
            ];
            $this->status_yang_mengerjakan = '';
            $this->status_pekerjaan = '';
        }
    }
    
    private function getJenisPekerjaanFromYangMengerjakan($yangMengerjakan)
    {
        // Extract job desk dari format "Nama (Job Desk)"
        preg_match('/\((.*?)\)/', $yangMengerjakan, $matches);
        $jobDesk = $matches[1] ?? '';
        
        // List job desk untuk PBG
        $pbgJobDesks = ['IRK', 'Gambar Arsitek', 'Gambar Struktur', 'Gambar MEP', 'SKA Arsitek', 'SKA Struktur', 'SKA MEP', 'Proses PBG'];
        
        // List job desk untuk SLF
        $slfJobDesks = ['Pengukuran', 'IRK', 'Gambar Arsitek', 'Gambar Struktur', 'Gambar MEP', 'SKA', 'Kajian', 'Proses SLF'];
        
        // Check if job desk is in PBG list
        if (in_array($jobDesk, $pbgJobDesks)) {
            return 'PBG';
        }
        
        // Check if job desk is in SLF list
        if (in_array($jobDesk, $slfJobDesks)) {
            return 'SLF';
        }
        
        // Default: ambil dari selected_jenis_pekerjaan pertama
        return $this->selected_jenis_pekerjaan[0] ?? '';
    }

    public function removeJobDeskAssignment($index)
    {
        unset($this->job_desk_assignments[$index]);
        $this->job_desk_assignments = array_values($this->job_desk_assignments);
    }

    public function save() {
        $this->validate([
            'nama_proyek' => 'required|min:3',
            'selected_owners' => 'required|array|min:1',
            'selected_jenis_pekerjaan' => 'required|array|min:1',
            'selected_yang_mengerjakan' => 'required|array|min:1',
            'job_desk_assignments' => 'required|array|min:1'
        ]);

        // Normalisasi data sebelum disimpan
        $normalizedData = $this->normalizeInputData();

        // Simpan setiap baris yang sudah dinormalisasi
        $savedCount = 0;
        foreach ($normalizedData as $data) {
            $project = new Project();
            $project->nama_proyek = $data['nama_proyek'];
            $project->owner = $data['owner'];
            $project->jenis_pekerjaan = $data['jenis_pekerjaan'];
            $project->yang_mengerjakan = $data['yang_mengerjakan'];
            $project->status_pekerjaan = $data['status_pekerjaan'];
            
            $project->save();
            $savedCount++;
        }

        if ($savedCount > 1) {
            session()->flash('success', "Proyek berhasil ditambahkan. Data dinormalisasi menjadi {$savedCount} baris.");
        } else {
            session()->flash('success', 'Proyek berhasil ditambahkan.');
        }

        $this->redirect('/projects');
    }

    /**
     * Normalisasi data input menjadi multiple records
     */
    private function normalizeInputData(): array
    {
        $normalizedData = [];
        
        // Setiap job desk assignment menjadi satu record
        foreach ($this->job_desk_assignments as $assignment) {
            $normalizedData[] = [
                'nama_proyek' => $this->nama_proyek,
                'owner' => $this->selected_owners[0] ?? '', // Ambil owner pertama atau bisa di-loop juga
                'jenis_pekerjaan' => $assignment['jenis_pekerjaan'],
                'yang_mengerjakan' => $assignment['yang_mengerjakan'],
                'status_pekerjaan' => $assignment['keterangan']
            ];
        }

        return $normalizedData;
    }

    /**
     * Split data berdasarkan newline dan bersihkan whitespace
     */
    private function splitData(?string $data): array
    {
        if (empty($data)) {
            return [''];
        }

        return array_filter(
            array_map('trim', explode("\n", $data)),
            function ($item) {
                return !empty($item);
            }
        );
    }

    public function render()
    {        
        return view('livewire.projects.create-project');
    }
}
