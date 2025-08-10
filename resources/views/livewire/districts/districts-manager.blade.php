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
        <flux:heading size="xl" level="1">{{ __('Districts') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage districts and their information') }}
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

            <flux:select wire:model.live="countyFilter" placeholder="Filter by county">
                <flux:select.option value="">All Counties</flux:select.option>
                @foreach ($counties as $county)
                    <flux:select.option value="{{ $county->id }}">{{ $county->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <flux:button wire:click="create" icon="plus-circle" variant="primary" color="teal">New District</flux:button>
    </div>

    <div class="overflow-x-auto mt-4">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3 cursor-pointer" wire:click="sortBy('code')">
                        Code
                        @if ($sortField === 'code')
                            @if ($sortDirection === 'asc')
                                ↑
                            @else
                                ↓
                            @endif
                        @endif
                    </th>
                    <th class="px-6 py-3">County</th>
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
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($districts as $district)
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4">DIST-{{ $district->id }}</td>
                        <td class="px-6 py-4">{{ $district->code }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $district->county->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $district->name }}
                        </td>
                        <td class="px-6 py-4">
                            @if ($district->is_active)
                                <flux:badge variant="solid" color="green">Active</flux:badge>
                            @else
                                <flux:badge variant="solid" color="red">Inactive</flux:badge>
                            @endif
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-center space-x-2 text-sm">
                            <button wire:click="show({{ $district->id }})"
                                class="text-indigo-600 hover:text-indigo-900 focus:outline-none" title="Show Details">
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                            <button wire:click="edit({{ $district->id }})"
                                class="text-yellow-600 hover:text-yellow-900 focus:outline-none" title="Edit District">
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536M9 13l6-6 3.536 3.536-6 6H9v-3.536z" />
                                </svg>
                            </button>
                            <button wire:click="confirmDelete({{ $district->id }})"
                                class="text-red-600 hover:text-red-900 focus:outline-none" title="Delete District">
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
                        <td colspan="6" class="px-6 py-4">No districts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $districts->links() }}
    </div>

    <!-- Create/Edit Modal -->
    @if ($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 overflow-y-auto p-4">
            <div class="w-full max-w-lg bg-white dark:bg-gray-900 rounded-lg shadow-lg p-6">
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
                        {{ $districtId ? 'Edit District' : 'Create District' }}
                    </h2>

                    <flux:select wire:model="county_id" :label="__('County')" placeholder="Select county...">
                        @foreach ($counties as $county)
                            <flux:select.option value="{{ $county->id }}">{{ $county->name }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input icon:trailing="map-pin" placeholder="Enter district name" type="text"
                        wire:model="name" label="District Name" />

                    <flux:input icon:trailing="hashtag" placeholder="District code" type="text" wire:model="code"
                        label="District Code" readonly />

                    {{-- <div class="flex items-center space-x-2">
                        <input type="checkbox" id="is_active" wire:model="is_active" class="form-checkbox h-4 w-4">
                        <label for="is_active" class="text-sm font-medium text-gray-700">Active District</label>
                    </div> --}}

                    <flux:fieldset>
                        <flux:legend>Status</flux:legend>

                        <flux:description>Set the district’s status.</flux:description>

                        <div class="flex gap-4 *:gap-x-2">
                            <flux:checkbox id="is_active" wire:model="is_active" value="1"
                                label="Active District" />
                        </div>
                    </flux:fieldset>



                    <div class="flex justify-end gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <flux:button icon="x-circle" variant="danger" wire:click="closeModal">
                            Cancel
                        </flux:button>
                        <flux:button icon="check-badge" variant="primary" color="green" type="submit">
                            {{ $districtId ? 'Update' : 'Save' }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Show Modal -->
    @if ($showModal && $selectedDistrict)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 relative">
                <div class="border rounded-xl overflow-hidden bg-white shadow-lg p-6">
                    <div class="p-4 border-b flex items-center justify-between bg-gray-100">
                        <h3 class="text-lg font-semibold dark:text-gray-900">District Details</h3>
                        <button wire:click="closeModal" class="text-gray-800 hover:text-gray-700">
                            ✖
                        </button>
                    </div>

                    <div class="mt-4 space-y-4">
                        <div>
                            <p class="text-sm text-gray-700">County</p>
                            <p class="font-medium dark:text-gray-900">{{ $selectedDistrict->county->name ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-700">District Name</p>
                            <p class="font-medium dark:text-gray-900">{{ $selectedDistrict->name }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-700">District Code</p>
                            <p class="font-medium dark:text-gray-900">{{ $selectedDistrict->code }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-700">Status</p>
                            <p class="font-medium dark:text-gray-900">
                                @if ($selectedDistrict->is_active)
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
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($confirmingDeletion)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-40"
            x-data="{ show: true }" x-show="show" x-transition>
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg w-full max-w-md mx-4 p-6">
                <h2 class="text-xl font-semibold mb-4">Confirm Delete</h2>
                <p>Are you sure you want to delete this district?</p>
                <div class="mt-4 flex justify-end space-x-2">
                    <button wire:click="delete" class="bg-red-600 text-white px-4 py-2 rounded">Yes, Delete</button>
                    <button wire:click="$set('confirmingDeletion', false)"
                        class="bg-gray-300 text-gray-800 px-4 py-2 rounded">Cancel</button>
                </div>
            </div>
        </div>
    @endif
</div>
