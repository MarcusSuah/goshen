<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserManager extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'tailwind';

    public $name,
        $email,
        $username,
        $password,
        $phone,
        $status = 'Pending',
        $userId;
    public $avatar, $avatarPreview, $currentAvatar;
    public $allRoles;
    public $roles = [];

    public $isOpen = false;
    public $confirmingDeletion = false;
    public $userToDelete;
    public $showModal = false;
    public $selectedUser;
    public $search = '';
    public $statusFilter = '';
    public $roleFilter = '';
    public $statusOptions = ['Pending', 'Active', 'Suspended', 'Approved'];
    public $sortField = 'name';
    public $sortDirection = 'asc';

    public function render()
    {
        $users = User::with('roles')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('username', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('status', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->roleFilter, function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', $this->roleFilter);
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.users.user-manager', [
            'users' => $users,
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
    public function mount()
    {
        $this->allRoles = Role::all()->pluck('name')->toArray();
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
        $this->selectedUser = null;
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->email = '';
        $this->username = '';
        $this->password = '';
        $this->phone = '';
        $this->status = 'Pending';
        $this->userId = null;
        $this->avatar = null;
        $this->avatarPreview = null;
        $this->currentAvatar = null;
    }

    public function updatedAvatar()
    {
        $this->validate([
            'avatar' => 'nullable|image|max:1024',
        ]);

        if ($this->avatar) {
            $this->avatarPreview = $this->avatar->temporaryUrl();
        }
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'username' => 'required|string|unique:users,username,' . $this->userId,
            'password' => $this->userId ? 'nullable|min:8' : 'required|min:8',
            'avatar' => 'nullable|image|max:2048',
            'phone' => 'nullable|string',
            'roles' => 'required|array|min:1',

        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'phone' => $this->phone,
            'status' => $this->status ?? 'Pending',
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->avatar) {
            $data['avatar'] = $this->avatar->store('avatars', 'public');
        }

        $user = User::updateOrCreate(['id' => $this->userId], $data);
        $user->syncRoles($this->roles);

        session()->flash('message', $this->userId ? 'User Updated Successfully!' : 'User Created Successfully!');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);

        $this->userId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->username = $user->username;
        $this->phone = $user->phone;
        $this->status = $user->status;
        $this->roles = $user->roles->pluck('name')->toArray();
        $this->currentAvatar = $user->avatar;

        $this->openModal();
    }

    public function removeAvatar()
    {
        $this->avatar = null;
        $this->avatarPreview = null;
    }

    public function show($id)
    {
        $this->selectedUser = User::with('roles')->findOrFail($id);
        $this->showModal = true;
        $this->isOpen = true;
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeletion = true;
        $this->userToDelete = $id;
    }

    public function delete()
    {
        User::findOrFail($this->userToDelete)->delete();
        $this->confirmingDeletion = false;

        session()->flash('message', 'User Deleted Successfully!');
    }
}
