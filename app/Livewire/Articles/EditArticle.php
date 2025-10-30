<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Component;

class EditProject extends Component
{
    public $id;
    public $nama_proyek;
    public $owner;
    public $jenis_pekerjaan;
    public $yang_mengerjakan;
    public $status_pekerjaan;

    public function mount($id)
    {
        $project = Project::findOrFail($id);
        $this->id = $project->id;
        $this->nama_proyek = $project->nama_proyek;
        $this->owner = $project->owner;
        $this->jenis_pekerjaan = $project->jenis_pekerjaan;
        $this->yang_mengerjakan = $project->yang_mengerjakan;
        $this->status_pekerjaan = $project->status_pekerjaan;
    }

    public function update()
    {

        $this->validate([
            'nama_proyek' => 'required|min:3',
            'owner' => 'required|min:2',
            'jenis_pekerjaan' => 'required',
            'yang_mengerjakan' => 'required',
            'status_pekerjaan' => 'required'
        ]);

        $project = Project::find($this->id);
        $project->nama_proyek = $this->nama_proyek;
        $project->owner = $this->owner;
        $project->jenis_pekerjaan = $this->jenis_pekerjaan;
        $project->yang_mengerjakan = $this->yang_mengerjakan;
        $project->status_pekerjaan = $this->status_pekerjaan;
        $project->save();

        session()->flash('success', 'Proyek berhasil diperbarui.');

        $this->redirect('/projects');
    }

    public function render()
    {
        return view('livewire.projects.edit-project');
    }
}
