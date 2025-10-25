<?php

namespace App\Livewire\Articles;

use App\Models\Project;
use Livewire\Component;

class CreateArticle extends Component
{
    public $nama_proyek = '';
    
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

    public function save() {
        $this->validate([
            'nama_proyek' => 'required|min:3',
            'selected_owners' => 'required|array|min:1',
            'selected_jenis_pekerjaan' => 'required|array|min:1',
            'selected_yang_mengerjakan' => 'required|array|min:1',
            'status_pekerjaan' => 'required'
        ]);

        $project = new Project();
        $project->nama_proyek = $this->nama_proyek;
        $project->owner = implode("\n", $this->selected_owners);
        $project->jenis_pekerjaan = implode("\n", $this->selected_jenis_pekerjaan);
        $project->yang_mengerjakan = implode("\n", $this->selected_yang_mengerjakan);
        $project->status_pekerjaan = $this->status_pekerjaan;
        $project->save();

        session()->flash('success', 'Proyek berhasil ditambahkan.');

        $this->redirect('/projects');
    }

    public function render()
    {        
        return view('livewire.articles.create-article');
    }
}
