<?php

namespace App\Livewire\Leaderships;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LeadershipPosition;
use Illuminate\Support\Str;

class LeadershipManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $title,
        $description,
        $hierarchy_level = 1,
        $is_active = true,
        $positionId;
    public $isOpen = false;
    public $confirmingDeletion = false;
    public $positionToDelete;
    public $showModal = false;
    public $selectedPosition;
    public $search = '';
    public $statusFilter = '';
    public $levelFilter = '';
    public $sortField = 'title';
    public $sortDirection = 'asc';
    public $hierarchyLevels = [
        1 => 'Community Level',
        2 => 'Block Level',
        3 => 'Street Level',
        4 => 'Other',
    ];
    public function render()
    {
        $positions = LeadershipPosition::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_active', $this->statusFilter === 'active');
            })
            ->when($this->levelFilter, function ($query) {
                $query->where('hierarchy_level', $this->levelFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.leaderships.leadership-manager', [
            'positions' => $positions,
        ]);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->showModal = false;
        $this->selectedPosition = null;
    }

    public function resetInputFields()
    {
        $this->title = '';
        $this->description = '';
        $this->hierarchy_level = 1;
        $this->is_active = true;
        $this->positionId = null;
    }

    public function store()
    {
        $this->validate([
            'title' => 'required|string|max:255|unique:leadership_positions,title,' . $this->positionId,
            'description' => 'nullable|string',
            'hierarchy_level' => 'required|integer|min:1|max:4',
            'is_active' => 'boolean',
        ]);

        LeadershipPosition::updateOrCreate(
            ['id' => $this->positionId],
            [
                'title' => $this->title,
                'description' => $this->description,
                'hierarchy_level' => $this->hierarchy_level,
                'is_active' => $this->is_active,
            ],
        );

        session()->flash('message', $this->positionId ? 'Position Updated Successfully!' : 'Position Created Successfully!');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $position = LeadershipPosition::findOrFail($id);

        $this->positionId = $id;
        $this->title = $position->title;
        $this->description = $position->description;
        $this->hierarchy_level = $position->hierarchy_level;
        $this->is_active = $position->is_active;

        $this->openModal();
    }

    public function show($id)
    {
        $this->selectedPosition = LeadershipPosition::findOrFail($id);
        $this->showModal = true;
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeletion = true;
        $this->positionToDelete = $id;
    }

    public function delete()
    {
        LeadershipPosition::findOrFail($this->positionToDelete)->delete();
        $this->confirmingDeletion = false;
        session()->flash('message', 'Position Deleted Successfully!');
    }
}
