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
        <flux:heading size="xl" level="1">{{ __('Leaders') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage community leaders and their information') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex justify-between items-center mb-4">
        <div class="flex space-x-4">
            <flux:input type="text" wire:model.live.debounce.500ms="search" class="w-md" icon="magnifying-glass"
                placeholder="Search..." />

            <flux:select wire:model.live="statusFilter" placeholder="Filter by status">
                <flux:select.option value="">All Statuses</flux:select.option>
                <flux:select.option value="active">Active</flux:select.option>
                <flux:select.option value="inactive">Inactive</flux:select.option>
            </flux:select>

            <flux:select wire:model.live="positionFilter" placeholder="Filter by position">
                <flux:select.option value="">All Positions</flux:select.option>
                @foreach ($positions as $position)
                    <flux:select.option value="{{ $position->id }}">{{ $position->title }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="typeFilter" placeholder="Filter by type">
                <flux:select.option value="">All Types</flux:select.option>
                @foreach ($leaderableTypes as $type)
                    <flux:select.option value="{{ $type }}">{{ $type }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <flux:button wire:click="create" icon="plus-circle" variant="primary" color="teal">Create New Leader
        </flux:button>
    </div>

    <div class="overflow-x-auto mt-4">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Image</th>
                    <th class="px-6 py-3 cursor-pointer" wire:click="sortBy('first_name')">
                        Name
                        @if ($sortField === 'first_name')
                            @if ($sortDirection === 'asc')
                                ↑
                            @else
                                ↓
                            @endif
                        @endif
                    </th>
                    <th class="px-6 py-3">Position</th>
                    <th class="px-6 py-3">Area</th>
                    <th class="px-6 py-3">Contact</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($leaders as $leader)
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4">LDR-{{ $leader->id }}</td>
                        <td class="px-6 py-4">
                            @if ($leader->image)
                                <img class="w-10 h-10 rounded-full" src="{{ asset('storage/' . $leader->image) }}"
                                    alt="{{ $leader->full_name }}">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span
                                        class="text-gray-500">{{ strtoupper(substr($leader->first_name, 0, 1)) }}</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $leader->full_name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $leader->position->title ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $leader->leaderable->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            @if ($leader->phone || $leader->email)
                                <div class="text-sm">
                                    @if ($leader->phone)
                                        {{ $leader->phone }}<br>
                                    @endif
                                    @if ($leader->email)
                                        {{ $leader->email }}
                                    @endif
                                </div>
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if ($leader->is_active)
                                <flux:badge variant="solid" color="green">Active</flux:badge>
                            @else
                                <flux:badge variant="solid" color="red">Inactive</flux:badge>
                            @endif
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-center space-x-2 text-sm">
                            <button wire:click="show({{ $leader->id }})"
                                class="text-indigo-600 hover:text-indigo-900 focus:outline-none" title="Show Details">
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                            <button wire:click="edit({{ $leader->id }})"
                                class="text-yellow-600 hover:text-yellow-900 focus:outline-none" title="Edit Leader">
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536M9 13l6-6 3.536 3.536-6 6H9v-3.536z" />
                                </svg>
                            </button>
                            <button wire:click="confirmDelete({{ $leader->id }})"
                                class="text-red-600 hover:text-red-900 focus:outline-none" title="Delete Leader">
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
                        <td colspan="8" class="px-6 py-4">No leaders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $leaders->links() }}
    </div>

    <!-- Create/Edit Modal -->
    @if ($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 overflow-y-auto p-4">
            <div class="w-full max-w-4xl bg-white dark:bg-gray-900 rounded-lg shadow-lg p-6">
                @if (count($errors) > 0)
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-red-600 text-sm">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul class="mt-1 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form wire:submit.prevent="store" class="space-y-4">
                    <h2 class="text-xl font-semibold mb-4 text-rose-600 dark:text-rose-500">
                        {{ $leaderId ? 'Edit Community Leader' : 'Create Community Leader' }}
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-center">
                                @if ($imagePreview)
                                    <img src="{{ $imagePreview }}"
                                        class="w-20 h-20 rounded-full object-cover border-4 border-gray-200">
                                @elseif($currentImage)
                                    <img src="{{ asset('storage/' . $currentImage) }}"
                                        class="w-20 h-20 rounded-full object-cover border-4 border-gray-200">
                                @else
                                    <div
                                        class="w-20 h-20 rounded-full bg-gray-200 border-4 border-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500 text-4xl">?</span>
                                    </div>
                                @endif
                            </div>

                            <flux:input wire:model="image" type="file" label="Leader Image" accept="image/*" />

                            <flux:input icon:trailing="user" placeholder="First Name" wire:model="first_name"
                                label="First Name" required />

                            <flux:input icon:trailing="user" placeholder="Last Name" wire:model="last_name"
                                label="Last Name" required />

                            <flux:input icon:trailing="user" placeholder="Middle Name" wire:model="middle_name"
                                label="Middle Name" />

                            <flux:select wire:model="leadership_position_id" :label="__('Leadership Position')"
                                placeholder="Select position..." required>
                                @foreach ($positions as $position)
                                    <flux:select.option value="{{ $position->id }}">{{ $position->title }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4 grid grid-cols-1 md:grid-cols-1 gap-2">

                            <div class="grid grid-cols-2 gap-3">
                                <flux:select wire:model="leaderable_type" :label="__('Leadership Area Type')"
                                    placeholder="Select type..." required>
                                    @foreach ($leaderableTypes as $type)
                                        <flux:select.option value="{{ $type }}">{{ $type }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>

                                {{-- <flux:select wire:model="leaderable_id" :label="__('Leadership Area')"
                                    placeholder="Select area..." required>
                                    @foreach ($leaderables as $leaderable)
                                        <flux:select.option value="{{ $leaderable->id }}">{{ $leaderable->name }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select> --}}
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <flux:input icon:trailing="phone" placeholder="Phone Number" wire:model="phone"
                                    label="Phone" type="tel" />

                                <flux:input icon:trailing="envelope" placeholder="Email Address" wire:model="email"
                                    label="Email" type="email" />
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <flux:select wire:model="gender" :label="__('Gender')" required>
                                    <flux:select.option value="Male">Male</flux:select.option>
                                    <flux:select.option value="Female">Female</flux:select.option>
                                    <flux:select.option value="Other">Other</flux:select.option>
                                </flux:select>

                                <flux:input icon:trailing="calendar" placeholder="Date of Birth"
                                    wire:model="date_of_birth" label="Date of Birth" type="date" />

                                <flux:input icon:trailing="calendar" placeholder="Appointment Date"
                                    wire:model="appointment_date" label="Appointment Date" type="date" required />

                                <flux:input icon:trailing="calendar" placeholder="Term End Date"
                                    wire:model="term_end_date" label="Term End Date" type="date" />
                            </div>

                            <div class="grid grid-cols-2 gap-3">

                                <flux:fieldset>
                                    <flux:legend>Status</flux:legend>

                                    <flux:description>Set the leader status.</flux:description>

                                    <div class="flex gap-4 *:gap-x-2">
                                        <flux:checkbox id="is_active" wire:model="is_active" value="1"
                                            label="Active Leader" />
                                    </div>
                                </flux:fieldset>

                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <flux:button icon="x-circle" variant="danger" wire:click="closeModal">
                            Cancel
                        </flux:button>
                        <flux:button icon="check-badge" variant="primary" color="green" type="submit">
                            {{ $leaderId ? 'Update' : 'Save' }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Show Modal -->
    @if ($showModal && $selectedLeader)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4 p-6">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="text-lg font-semibold">Leader Details</h3>
                    <button wire:click="closeModal" class="text-gray-500 hover:text-gray-700">
                        ✖
                    </button>
                </div>

                <div class="mt-4 space-y-4">
                    <div class="flex flex-col items-center">
                        @if ($selectedLeader->image)
                            <img src="{{ asset('storage/' . $selectedLeader->image) }}"
                                class="w-32 h-32 rounded-full object-cover border-4 border-gray-200">
                        @else
                            <div
                                class="w-32 h-32 rounded-full bg-gray-200 border-4 border-gray-200 flex items-center justify-center">
                                <span
                                    class="text-gray-500 text-4xl">{{ strtoupper(substr($selectedLeader->first_name, 0, 1)) }}</span>
                            </div>
                        @endif
                        <h4 class="mt-2 text-xl font-semibold">{{ $selectedLeader->full_name }}</h4>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Position</p>
                            <p class="font-medium">{{ $selectedLeader->position->title ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Leadership Area</p>
                            <p class="font-medium">{{ $selectedLeader->leaderable->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Gender</p>
                            <p class="font-medium">{{ $selectedLeader->gender }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Date of Birth</p>
                            <p class="font-medium">{{ $selectedLeader->date_of_birth?->format('M d, Y') ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Appointment Date</p>
                            <p class="font-medium">{{ $selectedLeader->appointment_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Term End Date</p>
                            <p class="font-medium">{{ $selectedLeader->term_end_date?->format('M d, Y') ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Contact Information</p>
                        <div class="font-medium">
                            @if ($selectedLeader->phone)
                                <p>{{ $selectedLeader->phone }}</p>
                            @endif
                            @if ($selectedLeader->email)
                                <p>{{ $selectedLeader->email }}</p>
                            @endif
                            @if (!$selectedLeader->phone && !$selectedLeader->email)
                                <p>N/A</p>
                            @endif
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <p class="font-medium">
                            @if ($selectedLeader->is_active)
                                <flux:badge variant="solid" color="green">Active</flux:badge>
                            @else
                                <flux:badge variant="solid" color="red">Inactive</flux:badge>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <flux:button icon="x-circle" variant="danger" wire:click="closeModal">
                        Close
                    </flux:button>
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
                <p>Are you sure you want to delete this leader?</p>
                <div class="mt-4 flex justify-end space-x-2">
                    <button wire:click="delete" class="bg-red-600 text-white px-4 py-2 rounded">Yes, Delete</button>
                    <button wire:click="$set('confirmingDeletion', false)"
                        class="bg-gray-300 text-gray-800 px-4 py-2 rounded">Cancel</button>
                </div>
            </div>
        </div>
    @endif
</div>
