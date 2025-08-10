<?php

namespace App\Livewire\Communities;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Community;
use App\Models\District;
use Illuminate\Support\Str;

class CommunityManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $name,
        $code,
        $district_id,
        $latitude,
        $longitude,
        $is_active = true,
        $communityId;
    public $isOpen = false;
    public $confirmingDeletion = false;
    public $communityToDelete;
    public $showModal = false;
    public $selectedCommunity;
    public $search = '';
    public $statusFilter = '';
    public $districtFilter = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $districts = [];

    public function mount()
    {
        $this->districts = District::where('is_active', true)->get();
    }
    public function render()
    {
        $communities = Community::with('district')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%')
                        ->orWhereHas('district', function ($q) {
                            $q->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_active', $this->statusFilter === 'active');
            })
            ->when($this->districtFilter, function ($query) {
                $query->where('district_id', $this->districtFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.communities.community-manager', [
            'communities' => $communities,
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
        $this->generateCommunityCode();
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
        $this->selectedCommunity = null;
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->code = '';
        $this->district_id = null;
        $this->latitude = null;
        $this->longitude = null;
        $this->is_active = true;
        $this->communityId = null;
    }

    protected function generateCommunityCode()
    {
        if (empty($this->name)) {
            $this->code = '';
            return;
        }

        $prefix = strtoupper(substr($this->name, 0, 4));
        $randomDigits = mt_rand(100, 999);
        $this->code = $prefix . $randomDigits;
    }

    public function updatedName()
    {
        if (!$this->communityId) {
            $this->generateCommunityCode();
        }
    }

    public function store()
    {
        $this->latitude = $this->cleanCoordinate($this->latitude);
        $this->longitude = $this->cleanCoordinate($this->longitude);

        $this->validate([
            'name' => 'required|string|max:255|unique:communities,name,' . $this->communityId . ',id,district_id,' . $this->district_id,
            'code' => 'required|string|max:7|unique:communities,code,' . $this->communityId,
            'district_id' => 'required|exists:districts,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
        ]);

        Community::updateOrCreate(
            ['id' => $this->communityId],
            [
                'name' => $this->name,
                'code' => $this->code,
                'district_id' => $this->district_id,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'is_active' => $this->is_active,
            ],
        );

        session()->flash('message', $this->communityId ? 'Community Updated Successfully!' : 'Community Created Successfully!');

        $this->closeModal();
        $this->resetInputFields();
    }

    protected function cleanCoordinate($coord)
    {
        if (empty($coord)) {
            return null;
        }

        // Remove degree symbol and direction
        $coord = str_replace(['°', 'N', 'S', 'E', 'W'], '', $coord);

        // Handle negative coordinates (S and W are negative)
        if (str_contains($coord, 'S') || str_contains($coord, 'W')) {
            $coord = '-' . str_replace(['S', 'W'], '', $coord);
        }

        return trim($coord);
    }

    public function edit($id)
    {
        $community = Community::findOrFail($id);

        $this->communityId = $id;
        $this->name = $community->name;
        $this->code = $community->code;
        $this->district_id = $community->district_id;
        $this->latitude = $community->latitude;
        $this->longitude = $community->longitude;
        $this->is_active = $community->is_active;

        $this->openModal();
    }

    public function show($id)
    {
        $this->selectedCommunity = Community::with('district')->findOrFail($id);
        $this->showModal = true;
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeletion = true;
        $this->communityToDelete = $id;
    }

    public function delete()
    {
        Community::findOrFail($this->communityToDelete)->delete();
        $this->confirmingDeletion = false;

        session()->flash('message', 'Community Deleted Successfully!');
    }
}
