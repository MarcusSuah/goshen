<?php

namespace App\Livewire\Districts;
use Livewire\WithPagination;
use App\Models\District;
use App\Models\County;
use Illuminate\Support\Str;
use Livewire\Component;

class DistrictsManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $name,
        $code,
        $county_id,
        $is_active = true,
        $districtId;
    public $isOpen = false;
    public $confirmingDeletion = false;
    public $districtToDelete;
    public $showModal = false;
    public $selectedDistrict;
    public $search = '';
    public $statusFilter = '';
    public $countyFilter = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $counties = [];

    public function mount()
    {
        $this->counties = County::where('is_active', true)->get();
    }

    public function render()
    {
        $districts = District::with('county')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%')
                        ->orWhereHas('county', function ($q) {
                            $q->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_active', $this->statusFilter === 'active');
            })
            ->when($this->countyFilter, function ($query) {
                $query->where('county_id', $this->countyFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.districts.districts-manager', [
            'districts' => $districts,
            'counties' => $this->counties,
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
        $this->generateDistrictCode();
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
        $this->selectedDistrict = null;
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->code = '';
        $this->county_id = null;
        $this->is_active = true;
        $this->districtId = null;
    }

    protected function generateDistrictCode()
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
        if (!$this->districtId) {
            $this->generateDistrictCode();
        }
    }

    public function store()
    {
        $this->validate([
            'county_id' => 'required|exists:counties,id',
            'name' => 'required|string|max:255|unique:districts,name,' . $this->districtId,
            'code' => 'required|string|max:7|unique:districts,code,' . $this->districtId,
            'is_active' => 'boolean',
        ]);

        District::updateOrCreate(
            ['id' => $this->districtId],
            [
                'name' => $this->name,
                'code' => $this->code,
                'county_id' => $this->county_id,
                'is_active' => $this->is_active,
            ],
        );

        session()->flash('message', $this->districtId ? 'District Updated Successfully!' : 'District Created Successfully!');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $district = District::findOrFail($id);

        $this->districtId = $id;
        $this->name = $district->name;
        $this->code = $district->code;
        $this->county_id = $district->county_id;
        $this->is_active = $district->is_active;

        $this->openModal();
    }

    public function show($id)
    {
        $this->selectedDistrict = District::with('county')->findOrFail($id);
        $this->showModal = true;
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeletion = true;
        $this->districtToDelete = $id;
    }

    public function delete()
    {
        District::findOrFail($this->districtToDelete)->delete();
        $this->confirmingDeletion = false;

        session()->flash('message', 'District Deleted Successfully!');
    }
}
