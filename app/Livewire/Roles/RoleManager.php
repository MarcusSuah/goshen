<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class RoleManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $name, $roleId;
    public $allPermissions = [];
    public $permissions = [];
    public $isOpen = false;
    public $confirmingDeletion = false;
    public $roleToDelete;
    public $showModal = false;
    public $selectedRole;
    public $search = '';
    public $permissionFilter = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    public function mount()
    {
        $this->allPermissions = Permission::get();
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

    public function render()
    {
        $roles = Role::with('permissions')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('id', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->permissionFilter, function ($query) {
                $query->whereHas('permissions', function ($q) {
                    $q->where('name', 'like', '%' . $this->permissionFilter . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.roles.role-manager', [
            'roles' => $roles,
        ]);
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
        $this->selectedRole = null;
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->permissions = [];
    }

    public function show($id)
    {
        $this->selectedRole = Role::findOrFail($id);
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $this->roleId,
            'permissions' => 'required|array|min:1',
        ]);

        if ($this->roleId) {
            $role = Role::findOrFail($this->roleId);
            $role->update(['name' => $this->name]);
        } else {
            $role = Role::create(['name' => $this->name]);
        }

        $role->syncPermissions($this->permissions);

        session()->flash('message', $this->roleId ? 'Role Updated Successfully!' : 'Role Created Successfully!');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);

        $this->roleId = $id;
        $this->name = $role->name;
        $this->permissions = $role->permissions->pluck('name')->toArray();

        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeletion = true;
        $this->roleToDelete = $id;
    }

    public function delete()
    {
        Role::findOrFail($this->roleToDelete)->delete();
        $this->confirmingDeletion = false;

        session()->flash('message', 'Role Deleted Successfully!');
    }
}
