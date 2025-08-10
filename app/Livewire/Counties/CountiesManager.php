<?php

namespace App\Livewire\Counties;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\County;
use Illuminate\Support\Str;

class CountiesManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'tailwind';

    public $name;
    public $city;
    public $code;
    public $is_active = true;
    public $countyId;
    public $isOpen = false;
    public $confirmingDeletion = false;
    public $countyToDelete;
    public $showModal = false;
    public $selectedCounty;
    public $search = '';
    public $statusFilter = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    public function render()
    {
        $counties = County::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('city', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_active', $this->statusFilter === 'active');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.counties.counties-manager', [
            'counties' => $counties,
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
        $this->generateCountyCode();
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
        $this->selectedCounty = null;
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->city = '';
        $this->code = '';
        $this->is_active = true;
        $this->countyId = null;
    }

    protected function generateCountyCode()
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
        if (!$this->countyId) {
            $this->generateCountyCode();
        }
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:counties,name,' . $this->countyId,
            'city' => 'required|string|max:255|unique:counties,city,' . $this->countyId,
            'code' => 'required|string|max:7|unique:counties,code,' . $this->countyId,
            'is_active' => 'boolean',
        ]);

        County::updateOrCreate(
            ['id' => $this->countyId],
            [
                'name' => $this->name,
                'city' => $this->city,
                'code' => $this->code,
                'is_active' => $this->is_active,
            ],
        );

        session()->flash('message', $this->countyId ? 'County Updated Successfully!' : 'County Created Successfully!');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $county = County::findOrFail($id);

        $this->countyId = $id;
        $this->name = $county->name;
        $this->city = $county->city;
        $this->code = $county->code;
        $this->is_active = $county->is_active;

        $this->openModal();
    }

    public function show($id)
    {
        $this->selectedCounty = County::findOrFail($id);
        $this->showModal = true;
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeletion = true;
        $this->countyToDelete = $id;
    }

    public function delete()
    {
        County::findOrFail($this->countyToDelete)->delete();
        $this->confirmingDeletion = false;

        session()->flash('message', 'County Deleted Successfully!');
    }
}
