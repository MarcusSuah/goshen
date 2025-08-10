<?php

namespace App\Livewire\Blocks;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Block;
use App\Models\Community;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BlockManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $name,
        $block_number,
        $community_id,
        $is_active = true,
        $blockId;
    public $isOpen = false;
    public $confirmingDeletion = false;
    public $blockToDelete;
    public $showModal = false;
    public $selectedBlock;
    public $search = '';
    public $statusFilter = '';
    public $communityFilter = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $communities = [];

    public function mount()
    {
        $this->communities = Community::where('is_active', true)->get();
    }

    public function render()
    {
        $blocks = Block::with('community')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('block_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('community', function ($q) {
                            $q->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_active', $this->statusFilter === 'active');
            })
            ->when($this->communityFilter, function ($query) {
                $query->where('community_id', $this->communityFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.blocks.block-manager', [
            'blocks' => $blocks,
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
        $this->generateBlockNumber();
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
        $this->selectedBlock = null;
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->block_number = '';
        $this->community_id = null;
        $this->is_active = true;
        $this->blockId = null;
    }

    protected function generateBlockNumber()
    {
        $prefix = 'BLK';
        $randomDigits = Str::random(3, '0123456789');
        $randomLetter = Str::random(1, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        $this->block_number = $prefix . $randomDigits . $randomLetter;
    }

    public function updatedName()
    {
        if (!$this->blockId) {
            $this->generateBlockNumber();
        }
    }

    public function store()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('blocks')->where('community_id', $this->community_id)->ignore($this->blockId)],
            'block_number' => ['required', 'string', 'max:8', Rule::unique('blocks')->ignore($this->blockId)],
            'community_id' => ['required', 'exists:communities,id'],
            'is_active' => ['boolean'],
        ]);

        Block::updateOrCreate(
            ['id' => $this->blockId],
            [
                'name' => $this->name,
                'block_number' => $this->block_number,
                'community_id' => $this->community_id,
                'is_active' => $this->is_active,
            ],
        );

        session()->flash('message', $this->blockId ? 'Block Updated Successfully!' : 'Block Created Successfully!');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $block = Block::findOrFail($id);

        $this->blockId = $id;
        $this->name = $block->name;
        $this->block_number = $block->block_number;
        $this->community_id = $block->community_id;
        $this->is_active = $block->is_active;

        $this->openModal();
    }

    public function show($id)
    {
        $this->selectedBlock = Block::with('community')->findOrFail($id);
        $this->showModal = true;
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeletion = true;
        $this->blockToDelete = $id;
    }

    public function delete()
    {
        Block::findOrFail($this->blockToDelete)->delete();
        $this->confirmingDeletion = false;
        session()->flash('message', 'Block Deleted Successfully!');
    }
}
