<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;

class ShowProjects extends Component
{
    use WithPagination;

    // Include search and sort parameters in the URL only when set.
    protected $queryString = [
        'keyword' => ['except' => ''],
        'sortColumn' => ['except' => null],
        'sortDirection' => ['except' => null],
    ];

    public $keyword = '';
    public $textInput = '';
    public $sortColumn = null;   // default: no sort column
    public $sortDirection = null; // default: no sort direction

    public function delete($id)
    {
        $project = Project::find($id);
        $project->delete();
        session()->flash('error', 'Proyek berhasil dihapus.');
        return $this->redirect('/projects');
    }

    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            if ($this->sortDirection === 'asc') {
                $this->sortDirection = 'desc';
            } elseif ($this->sortDirection === 'desc') {
                $this->resetSorting();
                return;
            }
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function resetSorting()
    {
        $this->sortColumn = null;
        $this->sortDirection = null;
    }

    public function render()
    {
        $projectsQuery = Project::query()
            ->when($this->keyword, function ($query) {
                $query->where('nama_proyek', 'like', '%' . $this->keyword . '%')
                    ->orWhere('owner', 'like', '%' . $this->keyword . '%')
                    ->orWhere('jenis_pekerjaan', 'like', '%' . $this->keyword . '%')
                    ->orWhere('yang_mengerjakan', 'like', '%' . $this->keyword . '%')
                    ->orWhere('status_pekerjaan', 'like', '%' . $this->keyword . '%');
            });

        // Apply sorting only if a column is selected.
        if ($this->sortColumn && $this->sortDirection) {
            $projectsQuery->orderBy($this->sortColumn, $this->sortDirection);
        }

        $projects = $projectsQuery->paginate(10);

        return view('livewire.projects.show-projects', compact('projects'));
    }

    public function search()
    {
        $this->keyword = $this->textInput;
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->textInput = '';
        $this->keyword = '';
        $this->resetPage();
    }

    // Method to clear both search and sorting at once.
    public function clearAll()
    {
        $this->clearSearch();
        $this->resetSorting();
    }
}
