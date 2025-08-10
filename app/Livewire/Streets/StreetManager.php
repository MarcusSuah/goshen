<?php

namespace App\Livewire\Streets;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Street;
use App\Models\Block;
use Illuminate\Support\Str;

class StreetManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $name,
        $street_type = 'Street',
        $block_id,
        $is_active = true,
        $streetId;
    public $isOpen = false;
    public $confirmingDeletion = false;
    public $streetToDelete;
    public $showModal = false;
    public $selectedStreet;
    public $search = '';
    public $statusFilter = '';
    public $blockFilter = '';
    public $typeFilter = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $blocks = [];
    public $streetTypes = ['Street', 'Avenue', 'Road', 'Boulevard', 'Lane', 'Drive', 'Court', 'Place', 'Way'];

    public function mount()
    {
        $this->blocks = Block::where('is_active', true)->get();
    }
    public function render()
    {
        $streets = Street::with('block')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('street_type', 'like', '%' . $this->search . '%')
                        ->orWhereHas('block', function ($q) {
                            $q->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_active', $this->statusFilter === 'active');
            })
            ->when($this->blockFilter, function ($query) {
                $query->where('block_id', $this->blockFilter);
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('street_type', $this->typeFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.streets.street-manager', [
            'streets' => $streets,
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
        $this->selectedStreet = null;
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->street_type = 'Street';
        $this->block_id = null;
        $this->is_active = true;
        $this->streetId = null;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'street_type' => 'required|string|in:' . implode(',', $this->streetTypes),
            'block_id' => 'required|exists:blocks,id',
            'is_active' => 'boolean',
        ]);

        Street::updateOrCreate(
            ['id' => $this->streetId],
            [
                'name' => $this->name,
                'street_type' => $this->street_type,
                'block_id' => $this->block_id,
                'is_active' => $this->is_active,
            ],
        );

        session()->flash('message', $this->streetId ? 'Street Updated Successfully!' : 'Street Created Successfully!');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $street = Street::findOrFail($id);

        $this->streetId = $id;
        $this->name = $street->name;
        $this->street_type = $street->street_type;
        $this->block_id = $street->block_id;
        $this->is_active = $street->is_active;

        $this->openModal();
    }

    public function show($id)
    {
        $this->selectedStreet = Street::with('block')->findOrFail($id);
        $this->showModal = true;
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeletion = true;
        $this->streetToDelete = $id;
    }

    public function delete()
    {
        Street::findOrFail($this->streetToDelete)->delete();
        $this->confirmingDeletion = false;
        session()->flash('message', 'Street Deleted Successfully!');
    }
}
