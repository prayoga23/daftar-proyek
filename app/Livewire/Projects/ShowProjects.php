<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Component;

class ShowProjects extends Component
{

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
    
    // Inline insert properties
    public $insertingAfterRowId = null;
    public $newRow = [
        'nama_proyek' => '',
        'owner' => '',
        'jenis_pekerjaan' => '',
        'yang_mengerjakan' => '',
        'status_pekerjaan' => '',
    ];
    
    // Highlighted projects (projects about to run)
    public $highlightedProjects = [];

    public function mount()
    {
        // Load highlighted projects from database
        $this->highlightedProjects = Project::where('highlighted', true)->pluck('id')->toArray();
    }

    public function toggleHighlight($projectId)
    {
        $project = Project::find($projectId);
        if ($project) {
            // Toggle the highlighted status in database
            $project->highlighted = !$project->highlighted;
            $project->save();

            // Update the local array
            if ($project->highlighted) {
                $this->highlightedProjects[] = $projectId;
            } else {
                $this->highlightedProjects = array_values(array_filter($this->highlightedProjects, function($id) use ($projectId) {
                    return $id !== $projectId;
                }));
            }
        }
    }

    public function markAsCompleted($id)
    {
        $project = Project::find($id);
        if ($project) {
            // Update semua record dengan nama_proyek yang sama menjadi "Selesai"
            $updatedCount = Project::where('nama_proyek', $project->nama_proyek)
                ->update(['status_pekerjaan' => 'Selesai']);
            session()->flash('message', "Proyek '{$project->nama_proyek}' telah ditandai sebagai selesai ({$updatedCount} record).");
        }
        return $this->redirect('/projects');
    }

    public function delete($id)
    {
        $project = Project::find($id);
        if ($project) {
            // Hapus semua record yang memiliki nama_proyek yang sama
            $deletedCount = Project::where('nama_proyek', $project->nama_proyek)->delete();
            session()->flash('error', "Proyek '{$project->nama_proyek}' berhasil dihapus ({$deletedCount} record).");
        }
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
        } else {
            // Default sorting: sort_order ascending (maintain insertion order)
            $projectsQuery->orderBy('sort_order', 'asc');
        }

        $projects = $projectsQuery->get();

        // No pagination, so groupOffset is always 0
        $groupOffset = 0;

        return view('livewire.projects.show-projects', compact('projects', 'groupOffset'));
    }

    public function search()
    {
        $this->keyword = $this->textInput;
    }

    public function clearSearch()
    {
        $this->textInput = '';
        $this->keyword = '';
    }

    // Method to clear both search and sorting at once.
    public function clearAll()
    {
        $this->clearSearch();
        $this->resetSorting();
    }
    
    // Inline insert methods
    public function showInsertRow($afterId)
    {
        // Reset all fields to empty
        $this->newRow['nama_proyek'] = '';
        $this->newRow['owner'] = '';
        $this->newRow['jenis_pekerjaan'] = '';
        $this->newRow['yang_mengerjakan'] = '';
        $this->newRow['status_pekerjaan'] = '';
        
        $this->insertingAfterRowId = $afterId;
    }
    
    public function cancelInsert()
    {
        $this->insertingAfterRowId = null;
        $this->reset('newRow');
    }
    
    public function saveNewRow()
    {
        // Validation
        $this->validate([
            'newRow.nama_proyek' => 'required|string|max:255',
            'newRow.owner' => 'required|string|max:255',
            'newRow.jenis_pekerjaan' => 'required|string|max:255',
            'newRow.yang_mengerjakan' => 'required|string|max:255',
            'newRow.status_pekerjaan' => 'required|string|max:255',
        ], [
            'newRow.nama_proyek.required' => 'Nama proyek harus diisi',
            'newRow.owner.required' => 'Owner harus diisi',
            'newRow.jenis_pekerjaan.required' => 'Jenis pekerjaan harus diisi',
            'newRow.yang_mengerjakan.required' => 'Yang mengerjakan harus diisi',
            'newRow.status_pekerjaan.required' => 'Status pekerjaan harus diisi',
        ]);
        
        // Calculate sort_order to insert right after the reference row
        $referenceProject = Project::find($this->insertingAfterRowId);
        if ($referenceProject) {
            // Get the next row's sort_order
            $nextProject = Project::where('sort_order', '>', $referenceProject->sort_order)
                ->orderBy('sort_order', 'asc')
                ->first();
            
            if ($nextProject) {
                // Insert between reference and next row
                // Shift all rows after reference forward by 1
                Project::where('sort_order', '>', $referenceProject->sort_order)
                    ->increment('sort_order', 1);
                
                $this->newRow['sort_order'] = $referenceProject->sort_order + 1;
            } else {
                // Insert at the end
                $this->newRow['sort_order'] = $referenceProject->sort_order + 1;
            }
        } else {
            // Fallback: insert at the end
            $maxSortOrder = Project::max('sort_order') ?? 0;
            $this->newRow['sort_order'] = $maxSortOrder + 1;
        }
        
        // Create new project
        Project::create($this->newRow);
        
        session()->flash('message', 'Baris baru berhasil ditambahkan!');
        
        // Reset form
        $this->cancelInsert();
    }
}
