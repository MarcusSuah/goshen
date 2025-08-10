<?php

namespace App\Livewire\Leaderships;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Leader;
use App\Models\LeadershipPosition;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class LeaderManager extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'tailwind';

    public $first_name, $last_name, $middle_name, $phone, $email;
    public $date_of_birth, $gender = 'Male', $appointment_date, $term_end_date;
    public $is_active = true, $leaderId, $leadership_position_id;
    public $leaderable_type, $leaderable_id, $image, $imagePreview, $currentImage;
    public $isOpen = false, $confirmingDeletion = false, $leaderToDelete;
    public $showModal = false, $selectedLeader;
    public $search = '', $statusFilter = '', $positionFilter = '', $typeFilter = '';
    public $sortField = 'first_name', $sortDirection = 'asc';
    public $positions = [], $leaderableTypes = ['Community', 'Block', 'Street'];
    public $leaderables = [];

    public function mount()
    {
        $this->positions = LeadershipPosition::where('is_active', true)->get();
        $this->appointment_date = now()->format('Y-m-d');
    }

    public function render()
    {
        $leaders = Leader::with(['position', 'leaderable'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                      ->orWhere('last_name', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhereHas('position', function ($q) {
                          $q->where('title', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_active', $this->statusFilter === 'active');
            })
            ->when($this->positionFilter, function ($query) {
                $query->where('leadership_position_id', $this->positionFilter);
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('leaderable_type', $this->typeFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.leaderships.leader-manager', [
            'leaders' => $leaders,
        ]);
    }

    public function updatedLeaderableType()
    {
        if ($this->leaderable_type) {
            $model = 'App\\Models\\' . $this->leaderable_type;
            $this->leaderables = $model::all();
        } else {
            $this->leaderables = [];
        }
    }

    public function updatedImage()
    {
        $this->validate([
            'image' => 'image|max:2048',
        ]);
        $this->imagePreview = $this->image->temporaryUrl();
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
        $this->selectedLeader = null;
    }

    public function resetInputFields()
    {
        $this->first_name = '';
        $this->last_name = '';
        $this->middle_name = '';
        $this->phone = '';
        $this->email = '';
        $this->date_of_birth = null;
        $this->gender = 'Male';
        $this->appointment_date = now()->format('Y-m-d');
        $this->term_end_date = null;
        $this->is_active = true;
        $this->leaderId = null;
        $this->leadership_position_id = null;
        $this->leaderable_type = null;
        $this->leaderable_id = null;
        $this->image = null;
        $this->imagePreview = null;
        $this->currentImage = null;
        $this->leaderables = [];
    }

    public function store()
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'required|in:Male,Female,Other',
            'appointment_date' => 'required|date',
            'term_end_date' => 'nullable|date|after:appointment_date',
            'is_active' => 'boolean',
            'leadership_position_id' => 'required|exists:leadership_positions,id',
            'leaderable_type' => 'required|string',
            'leaderable_id' => 'required',
            'image' => 'required_without:currentImage|image|max:2048',
        ]);

        $data = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'appointment_date' => $this->appointment_date,
            'term_end_date' => $this->term_end_date,
            'is_active' => $this->is_active,
            'leadership_position_id' => $this->leadership_position_id,
            'leaderable_type' => 'App\\Models\\' . $this->leaderable_type,
            'leaderable_id' => $this->leaderable_id,
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('leaders', 'public');
        }

        Leader::updateOrCreate(['id' => $this->leaderId], $data);

        session()->flash('message', $this->leaderId ? 'Leader Updated Successfully!' : 'Leader Created Successfully!');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $leader = Leader::findOrFail($id);

        $this->leaderId = $id;
        $this->first_name = $leader->first_name;
        $this->last_name = $leader->last_name;
        $this->middle_name = $leader->middle_name;
        $this->phone = $leader->phone;
        $this->email = $leader->email;
        $this->date_of_birth = $leader->date_of_birth?->format('Y-m-d');
        $this->gender = $leader->gender;
        $this->appointment_date = $leader->appointment_date->format('Y-m-d');
        $this->term_end_date = $leader->term_end_date?->format('Y-m-d');
        $this->is_active = $leader->is_active;
        $this->leadership_position_id = $leader->leadership_position_id;
        $this->leaderable_type = str_replace('App\\Models\\', '', $leader->leaderable_type);
        $this->leaderable_id = $leader->leaderable_id;
        $this->currentImage = $leader->image;

        $this->updatedLeaderableType();
        $this->openModal();
    }

    public function show($id)
    {
        $this->selectedLeader = Leader::with(['position', 'leaderable'])->findOrFail($id);
        $this->showModal = true;
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeletion = true;
        $this->leaderToDelete = $id;
    }

    public function delete()
    {
        $leader = Leader::findOrFail($this->leaderToDelete);
        if ($leader->image) {
            Storage::disk('public')->delete($leader->image);
        }
        $leader->delete();
        $this->confirmingDeletion = false;
        session()->flash('message', 'Leader Deleted Successfully!');
    }
}
