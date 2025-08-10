<div>
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
            class="fixed top-4 right-4 z-50 bg-green-500 border border-green-300 text-white dark:bg-green-800 dark:text-white-500 dark:border-green-600 px-4 py-3 rounded-lg shadow-md"
            role="alert">
            {{ session('message') }}
        </div>
    @endif

    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Users') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage your users and account') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex justify-between items-center mb-4">
        <div class="flex space-x-4">
            <flux:input type="text" wire:model.live.debounce.500ms="search" icon="magnifying-glass"
                placeholder="Search..." />

            <flux:select wire:model.live="statusFilter" placeholder="Filter by status">
                <flux:select.option value="">All Statuses</flux:select.option>
                @foreach ($statusOptions as $option)
                    <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="roleFilter" placeholder="Filter by role">
                <flux:select.option value="">All Roles</flux:select.option>
                @foreach ($allRoles as $role)
                    <flux:select.option value="{{ $role }}">{{ $role }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
        @can('create')
        <flux:button wire:click="create" icon="plus-circle" variant="primary" color="teal">New User</flux:button>
        @endcan
    </div>

    <div class="overflow-x-auto mt-4">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Avatar</th>
                    <th class="px-6 py-3 cursor-pointer" wire:click="sortBy('name')">
                        Name
                        @if ($sortField === 'name')
                            @if ($sortDirection === 'asc')
                                ↑
                            @else
                                ↓
                            @endif
                        @endif
                    </th>
                    <th class="px-6 py-3 cursor-pointer" wire:click="sortBy('username')">
                        Username
                        @if ($sortField === 'username')
                            @if ($sortDirection === 'asc')
                                ↑
                            @else
                                ↓
                            @endif
                        @endif
                    </th>
                    <th class="px-6 py-3 cursor-pointer" wire:click="sortBy('email')">
                        Email
                        @if ($sortField === 'email')
                            @if ($sortDirection === 'asc')
                                ↑
                            @else
                                ↓
                            @endif
                        @endif
                    </th>
                    <th class="px-6 py-3">Phone</th>
                    <th class="px-6 py-3">Roles</th>
                    <th class="px-6 py-3 cursor-pointer" wire:click="sortBy('status')">
                        Status
                        @if ($sortField === 'status')
                            @if ($sortDirection === 'asc')
                                ↑
                            @else
                                ↓
                            @endif
                        @endif
                    </th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4">USR-0{{ $user->id }}</td>

                        <td class="px-6 py-4">
                            @if ($user->avatar)
                                <img class="w-10 h-10 rounded-full" src="{{ asset('storage/' . $user->avatar) }}"
                                    alt="{{ $user->name }} avatar">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                            @endif
                        </td>

                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $user->name }}
                        </td>

                        <td class="px-6 py-4">{{ $user->username }}</td>
                        <td class="px-6 py-4">{{ $user->email }}</td>
                        <td class="px-6 py-4">{{ $user->phone }}</td>
                        <td class="px-6 py-4">
                            @foreach ($user->roles as $role)
                                <flux:badge variant="solid" color="orange">{{ $role->name }}</flux:badge>
                            @endforeach
                        </td>

                        <td class="py-2 px-4">
                            @if ($user->status == 'active')
                                <flux:badge variant="solid" color="green">{{ ucfirst($user->status) }}</flux:badge>
                            @elseif ($user->status == 'pending')
                                <flux:badge variant="solid" color="yellow">{{ ucfirst($user->status) }}</flux:badge>
                            @elseif ($user->status == 'suspended')
                                <flux:badge variant="solid" color="red">{{ ucfirst($user->status) }}</flux:badge>
                            @else
                                <flux:badge variant="solid" color="blue">{{ ucfirst($user->status) }}</flux:badge>
                            @endif
                        </td>


                        <td class="px-4 py-2 whitespace-nowrap text-center space-x-2 text-sm">
                            <button wire:click="show({{ $user->id }})"
                                class="text-indigo-600 hover:text-indigo-900 focus:outline-none" title="Show Details">
                                <!-- Eye icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                            <button wire:click="edit({{ $user->id }})"
                                class="text-yellow-600 hover:text-yellow-900 focus:outline-none" title="Edit Station">
                                <!-- Pencil icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536M9 13l6-6 3.536 3.536-6 6H9v-3.536z" />
                                </svg>
                            </button>
                            <button wire:click="confirmDelete({{ $user->id }})"
                                class="text-red-600 hover:text-red-900 focus:outline-none" title="Delete Station">
                                <!-- Trash icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7L5 7M10 11v6M14 11v6M5 7l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr class="text-center">
                        <td colspan="8" class="px-6 py-4">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>


    <!-- Create/Edit Modal -->
    @if ($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 overflow-y-auto">


            @if (count($errors) > 0)
                <div class="alert alert-danger text-red-500">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li class="text-red-500">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form wire:submit.prevent="store"
                class="space-y-4 dark:bg-gray-900 rounded-lg shadow-lg w-full max-w-lg mx-4 sm:mx-auto p-6">
                <h2 class="text-xl font-semibold mb-4 text-rose-600 dark:text-rose-600">

                    {{ $userId ? 'Edit User' : 'Create User' }}</h2>
                <div class="mb-3">
                    <flux:input icon:trailing="user-circle" placeholder="enter your full name" type="text"
                        wire:model="name" label="Full Name" />
                </div>
                <div class="mb-3">
                    <flux:input icon:trailing="shield-check" placeholder="enter your username" type="text"
                        wire:model="username" label="Username" />
                </div>

                <div class="mb-3">
                    <flux:input icon:trailing="envelope" placeholder="enter your email address" type="email"
                        wire:model="email" label="Email" />
                </div>
                <div class="mb-3">
                    <flux:input placeholder="enter your password" type="password" wire:model="password"
                        label="Password" viewable />
                </div>

                <div class="mb-3">
                    <flux:input icon:trailing="phone" placeholder="enter your phone number" type="text"
                        wire:model="phone" label="Phone number" />
                </div>

                <div class="flex space-x-2 items-center">
                    <flux:input wire:model="avatar" :label="__('Avatar')" type="file" />
                    @if ($avatarPreview)
                        <img src="{{ $avatarPreview }}" class="w-12 h-12 rounded-full object-cover" />
                    @elseif ($currentAvatar)
                        <img src="{{ asset('storage/' . $currentAvatar) }}"
                            class="w-12 h-12 rounded-full object-cover" />
                    @endif
                </div>

                <flux:checkbox.group wire:model="roles" label="Roles">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        @foreach ($allRoles as $role)
                            <flux:checkbox label="{{ $role }}" value="{{ $role }}" />
                        @endforeach
                    </div>
                </flux:checkbox.group>


                <flux:select wire:model.defer="status" :label="__('Status')" placeholder="Choose status...">
                    @foreach ($statusOptions as $option)
                        <flux:select.option value="{{ $option }}">{{ $option }}
                        </flux:select.option>
                    @endforeach
                </flux:select>

                <div class="text-right">
                    <flux:button icon="x-circle" variant="danger" wire:click="$set('isOpen', false)">Cancel
                    </flux:button>
                    <flux:button icon="check-badge" variant="primary" color="green" type="submit">
                        {{ $userId ? 'Update' : 'Save' }}</flux:button>
                </div>
            </form>

        </div>
    @endif



    <!-- Modal -->
    @if ($showModal && $selectedUser)

        <!-- Styled Show Modal -->
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 relative">

                <div class="border rounded-xl overflow-hidden  bg-white  shadow-lg  p-6">
                    <div class="p-4 border-b flex items-center justify-between bg-gray-100">
                        <span class="text-sm font-medium text-gray-700">User ID: USR-0{{ $selectedUser->id }}</span>
                        <span
                            class="inline-block text-xs px-3 py-1 rounded-full uppercase {{ $selectedUser->status === 'Active' ? 'bg-red-500 text-white' : 'bg-emerald-500 text-white' }}">
                            {{ $selectedUser->status }}
                        </span>
                    </div>

                    <div class="p-6 text-center">
                        <!-- Profile Image -->
                        <div class="w-24 h-24 mx-auto mb-4 rounded-full overflow-hidden border-4 border-green-500">
                            @if ($selectedUser->avatar)
                                <img class="w-40 h-20 rounded-full"
                                    src="{{ asset('storage/' . $selectedUser->avatar) }}"
                                    alt="{{ $selectedUser->name }} avatar">
                            @endif
                        </div>
                        <!-- User Info -->
                        <h3 class="text-lg font-semibold mb-1 dark:text-gray-800"> <span
                                class="font-medium text-gray-700">Name:
                            </span>{{ $selectedUser->name }}</h3>
                        <p class="text-sm text-gray-600 mb-2"><span class="font-medium text-gray-700">Email:
                            </span>{{ $selectedUser->email }}</p>
                        <p class="text-sm text-gray-600"><span class="font-medium text-gray-700">Phone: </span>
                            {{ $selectedUser->phone }}</p>
                        <p class="text-sm text-gray-500">Roles</p>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach ($selectedUser->roles as $role)
                                <flux:badge variant="solid" color="emerald">{{ $role->name }}</flux:badge>
                            @endforeach
                        </div>
                    </div>

                    <!-- Footer Info -->
                    <div class="bg-gray-50 px-4 py-3 text-left text-sm text-gray-500">
                        <div>
                            <span class="font-medium"> Created At:</span>
                            {{ $selectedUser->created_at->format('d M Y') }}
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <flux:button icon="x-circle" variant="danger" wire:click="closeModal">Close
                        </flux:button>
                    </div>
                    <!-- Actions -->
                </div>
            </div>
        </div>

    @endif


    <!-- Delete Confirmation Modal -->
    @if ($confirmingDeletion)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-40"
            x-data="{ show: true }" x-show="show" x-transition>
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg w-full max-w-md mx-4 p-6">
                <h2 class="text-xl font-semibold mb-4">Confirm Delete</h2>
                <p>Are you sure you want to delete this user?</p>
                <div class="mt-4 flex justify-end space-x-2">
                    <button wire:click="delete" class="bg-red-600 text-white px-4 py-2 rounded">Yes, Delete</button>
                    <button wire:click="$set('confirmingDeletion', false)"
                        class="bg-gray-300 text-gray-800 px-4 py-2 rounded">Cancel</button>
                </div>
            </div>
        </div>
    @endif

</div>
