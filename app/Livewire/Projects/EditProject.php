<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Component;

class EditProject extends Component
{
    public $id;
    public $nama_proyek;
    public $edit_field = 'all';
    public $edit_mode = 'bulk'; // bulk|row
    
    // Properties untuk input fields
    public $owner_input = '';
    public $yang_mengerjakan_input = '';
    public $jenis_pekerjaan_selected = '';
    public $status_jenis_pekerjaan = '';
    public $status_yang_mengerjakan = '';
    public $status_pekerjaan = '';
    
    // Arrays untuk menyimpan data yang dipilih
    public $selected_owners = [];
    public $selected_yang_mengerjakan = [];
    public $selected_jenis_pekerjaan = [];
    
    // PBG specific fields
    public $irk_pbg = '';
    public $gambar_arsitek_pbg = '';
    public $gambar_struktur_pbg = '';
    public $gambar_mep_pbg = '';
    public $ska_pbg = '';
    public $proses_pbg = '';
    
    // SLF specific fields
    public $pengukuran_slf = '';
    public $irk_slf = '';
    public $gambar_arsitek_slf = '';
    public $gambar_struktur_slf = '';
    public $gambar_mep_slf = '';
    public $ska_slf = '';
    public $kajian_slf = '';
    public $proses_slf = '';

    public function mount($id)
    {
        $project = Project::findOrFail($id);
        $this->id = $project->id;
        $this->nama_proyek = $project->nama_proyek;
        $this->status_pekerjaan = $project->status_pekerjaan;
        // Ambil kolom yang ingin diedit dari query string (?field=...)
        $this->edit_field = request()->query('field', 'all');
        $this->edit_mode = request()->query('mode', 'bulk');
        
        // Load data ke dalam arrays
        $this->selected_owners = $this->splitData($project->owner);
        $this->selected_jenis_pekerjaan = $this->splitData($project->jenis_pekerjaan);
        $this->selected_yang_mengerjakan = $this->splitData($project->yang_mengerjakan);
        
        // Load PBG fields
        $this->irk_pbg = $project->irk_pbg ?? '';
        $this->gambar_arsitek_pbg = $project->gambar_arsitek_pbg ?? '';
        $this->gambar_struktur_pbg = $project->gambar_struktur_pbg ?? '';
        $this->gambar_mep_pbg = $project->gambar_mep_pbg ?? '';
        $this->ska_pbg = $project->ska_pbg ?? '';
        $this->proses_pbg = $project->proses_pbg ?? '';
        
        // Load SLF fields
        $this->pengukuran_slf = $project->pengukuran_slf ?? '';
        $this->irk_slf = $project->irk_slf ?? '';
        $this->gambar_arsitek_slf = $project->gambar_arsitek_slf ?? '';
        $this->gambar_struktur_slf = $project->gambar_struktur_slf ?? '';
        $this->gambar_mep_slf = $project->gambar_mep_slf ?? '';
        $this->ska_slf = $project->ska_slf ?? '';
        $this->kajian_slf = $project->kajian_slf ?? '';
        $this->proses_slf = $project->proses_slf ?? '';
    }

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
        if (!empty($this->yang_mengerjakan_input)) {
            $this->selected_yang_mengerjakan[] = $this->yang_mengerjakan_input;
            $this->yang_mengerjakan_input = '';
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

    public function update()
    {
        // Mode edit per-baris: update hanya kolom yang dipilih pada record ini
        if ($this->edit_mode === 'row' && $this->edit_field !== 'all') {
            $rules = [];
            if ($this->edit_field === 'nama_proyek') {
                $rules['nama_proyek'] = 'required|min:3';
            } elseif ($this->edit_field === 'owner') {
                // Ambil satu nilai owner dari array jika ada
                $rules['selected_owners'] = 'required|array|min:1';
            } elseif ($this->edit_field === 'jenis_pekerjaan') {
                $rules['selected_jenis_pekerjaan'] = 'required|array|min:1';
            } elseif ($this->edit_field === 'yang_mengerjakan') {
                $rules['selected_yang_mengerjakan'] = 'required|array|min:1';
            } elseif ($this->edit_field === 'status_pekerjaan') {
                $rules['status_pekerjaan'] = 'required';
            }

            $this->validate($rules);

            $project = Project::findOrFail($this->id);
            switch ($this->edit_field) {
                case 'nama_proyek':
                    $project->nama_proyek = $this->nama_proyek;
                    break;
                case 'owner':
                    $project->owner = $this->selected_owners[0] ?? '';
                    break;
                case 'jenis_pekerjaan':
                    $project->jenis_pekerjaan = $this->selected_jenis_pekerjaan[0] ?? '';
                    
                    // Update PBG/SLF fields based on jenis_pekerjaan
                    if ($project->jenis_pekerjaan === 'PBG') {
                        $project->irk_pbg = $this->irk_pbg;
                        $project->gambar_arsitek_pbg = $this->gambar_arsitek_pbg;
                        $project->gambar_struktur_pbg = $this->gambar_struktur_pbg;
                        $project->gambar_mep_pbg = $this->gambar_mep_pbg;
                        $project->ska_pbg = $this->ska_pbg;
                        $project->proses_pbg = $this->proses_pbg;
                        
                        // Clear SLF fields
                        $project->pengukuran_slf = null;
                        $project->irk_slf = null;
                        $project->gambar_arsitek_slf = null;
                        $project->gambar_struktur_slf = null;
                        $project->gambar_mep_slf = null;
                        $project->ska_slf = null;
                        $project->kajian_slf = null;
                        $project->proses_slf = null;
                    } elseif ($project->jenis_pekerjaan === 'SLF') {
                        $project->pengukuran_slf = $this->pengukuran_slf;
                        $project->irk_slf = $this->irk_slf;
                        $project->gambar_arsitek_slf = $this->gambar_arsitek_slf;
                        $project->gambar_struktur_slf = $this->gambar_struktur_slf;
                        $project->gambar_mep_slf = $this->gambar_mep_slf;
                        $project->ska_slf = $this->ska_slf;
                        $project->kajian_slf = $this->kajian_slf;
                        $project->proses_slf = $this->proses_slf;
                        
                        // Clear PBG fields
                        $project->irk_pbg = null;
                        $project->gambar_arsitek_pbg = null;
                        $project->gambar_struktur_pbg = null;
                        $project->gambar_mep_pbg = null;
                        $project->ska_pbg = null;
                        $project->proses_pbg = null;
                    }
                    break;
                case 'yang_mengerjakan':
                    $project->yang_mengerjakan = $this->selected_yang_mengerjakan[0] ?? '';
                    break;
                case 'status_pekerjaan':
                    $project->status_pekerjaan = $this->status_pekerjaan;
                    break;
            }
            $project->save();
            session()->flash('success', 'Baris berhasil diperbarui.');
            $this->redirect('/projects');
            return;
        }

        // Mode bulk (semua atau beberapa kolom sekaligus): normalisasi
        // Validasi dinamis sesuai kolom yang dipilih untuk diedit
        $rules = [];
        if ($this->edit_field === 'all' || $this->edit_field === 'nama_proyek') {
            $rules['nama_proyek'] = 'required|min:3';
        }
        if ($this->edit_field === 'all' || $this->edit_field === 'owner') {
            $rules['selected_owners'] = 'required|array|min:1';
        }
        if ($this->edit_field === 'all' || $this->edit_field === 'jenis_pekerjaan') {
            $rules['selected_jenis_pekerjaan'] = 'required|array|min:1';
        }
        if ($this->edit_field === 'all' || $this->edit_field === 'yang_mengerjakan') {
            $rules['selected_yang_mengerjakan'] = 'required|array|min:1';
        }
        if ($this->edit_field === 'all' || $this->edit_field === 'status_pekerjaan') {
            $rules['status_pekerjaan'] = 'required';
        }

        $this->validate($rules);

        // Normalisasi data sebelum disimpan
        $normalizedData = $this->normalizeInputData();

        // Hapus record lama
        Project::where('id', $this->id)->delete();

        // Simpan setiap baris yang sudah dinormalisasi
        $savedCount = 0;
        foreach ($normalizedData as $data) {
            $project = new Project();
            $project->nama_proyek = $data['nama_proyek'];
            $project->owner = $data['owner'];
            $project->jenis_pekerjaan = $data['jenis_pekerjaan'];
            $project->yang_mengerjakan = $data['yang_mengerjakan'];
            $project->status_pekerjaan = $data['status_pekerjaan'];
            
            // Save PBG fields if jenis_pekerjaan is PBG
            if ($data['jenis_pekerjaan'] === 'PBG') {
                $project->irk_pbg = $this->irk_pbg;
                $project->gambar_arsitek_pbg = $this->gambar_arsitek_pbg;
                $project->gambar_struktur_pbg = $this->gambar_struktur_pbg;
                $project->gambar_mep_pbg = $this->gambar_mep_pbg;
                $project->ska_pbg = $this->ska_pbg;
                $project->proses_pbg = $this->proses_pbg;
            }
            
            // Save SLF fields if jenis_pekerjaan is SLF
            if ($data['jenis_pekerjaan'] === 'SLF') {
                $project->pengukuran_slf = $this->pengukuran_slf;
                $project->irk_slf = $this->irk_slf;
                $project->gambar_arsitek_slf = $this->gambar_arsitek_slf;
                $project->gambar_struktur_slf = $this->gambar_struktur_slf;
                $project->gambar_mep_slf = $this->gambar_mep_slf;
                $project->ska_slf = $this->ska_slf;
                $project->kajian_slf = $this->kajian_slf;
                $project->proses_slf = $this->proses_slf;
            }
            
            $project->save();
            $savedCount++;
        }

        if ($savedCount > 1) {
            session()->flash('success', "Proyek berhasil diperbarui. Data dinormalisasi menjadi {$savedCount} baris.");
        } else {
            session()->flash('success', 'Proyek berhasil diperbarui.');
        }

        $this->redirect('/projects');
    }

    /**
     * Normalisasi data input menjadi multiple records
     */
    private function normalizeInputData(): array
    {
        // Gunakan data dari arrays yang sudah dipilih
        $owners = $this->selected_owners;
        $jenisPekerjaans = $this->selected_jenis_pekerjaan;
        $yangMengerjakans = $this->selected_yang_mengerjakan;
        $statusPekerjaans = [$this->status_pekerjaan]; // Status pekerjaan hanya satu

        // Tentukan jumlah maksimal entries
        $maxEntries = max(
            count($owners),
            count($jenisPekerjaans),
            count($yangMengerjakans),
            count($statusPekerjaans)
        );

        $normalizedData = [];

        for ($i = 0; $i < $maxEntries; $i++) {
            $normalizedData[] = [
                'nama_proyek' => $this->nama_proyek,
                'owner' => $owners[$i] ?? $owners[0] ?? '',
                'jenis_pekerjaan' => $jenisPekerjaans[$i] ?? '',
                'yang_mengerjakan' => $yangMengerjakans[$i] ?? '',
                'status_pekerjaan' => $statusPekerjaans[0] ?? ''
            ];
        }

        return $normalizedData;
    }

    /**
     * Helper untuk menentukan apakah sebuah kolom sedang diedit
     */
    public function isEditing(string $field): bool
    {
        return $this->edit_field === 'all' || $this->edit_field === $field;
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
        return view('livewire.projects.edit-project');
    }
}
