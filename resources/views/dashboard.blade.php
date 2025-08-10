{{-- <x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-3 md:grid-cols-4">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>

            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>
</x-layouts.app> --}}

<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-3">

            <!-- Total Users -->
            <a href="{{ route('users.index') }}" role="link" tabindex="0"
                aria-label="Total users count: {{ \App\Models\User::count() }} users"
                class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700
                      transform transition duration-300 ease-in-out
                      hover:bg-blue-50 hover:dark:bg-blue-900 hover:shadow-lg hover:-translate-y-1 hover:scale-105
                      focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-600">
                <flux:badge variant="solid" color="blue" class="p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-5 sm:w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true" focusable="false">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M9 20H4v-2a3 3 0 015.356-1.857M15 11a4 4 0 10-6 0m6 0a4 4 0 01-6 0" />
                    </svg>
                </flux:badge>
                <div class="ml-4">
                    <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-200 sm:text-base">Total Users</h4>
                    <p class="text-gray-500 dark:text-gray-400 text-sm sm:text-xs">{{ \App\Models\User::count() }} Users
                    </p>
                </div>
            </a>

            <!-- Active Users -->
            <a href="{{ route('users.index') }}" role="link" tabindex="0"
                aria-label="Active users count: {{ \App\Models\User::where('status', 'Active')->count() }} users"
                class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700
                      transform transition duration-300 ease-in-out
                      hover:bg-green-50 hover:dark:bg-green-900 hover:shadow-lg hover:-translate-y-1 hover:scale-105
                      focus:outline-none focus:ring-4 focus:ring-green-300 dark:focus:ring-green-600">
                <flux:badge variant="solid" color="green" class="p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-5 sm:w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true" focusable="false">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </flux:badge>
                <div class="ml-4">
                    <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-200 sm:text-base">Active Users</h4>
                    <p class="text-gray-500 dark:text-gray-400 text-sm sm:text-xs">
                        {{ \App\Models\User::where('status', 'Active')->count() }} Active
                    </p>
                </div>
            </a>

            <!-- Pending Users -->
            <a href="{{ route('users.index') }}" role="link" tabindex="0"
                aria-label="Pending users count: {{ \App\Models\User::where('status', 'Pending')->count() }} users"
                class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700
                      transform transition duration-300 ease-in-out
                      hover:bg-yellow-50 hover:dark:bg-yellow-900 hover:shadow-lg hover:-translate-y-1 hover:scale-105
                      focus:outline-none focus:ring-4 focus:ring-yellow-300 dark:focus:ring-yellow-600">
                <flux:badge variant="solid" color="yellow" class="p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-5 sm:w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true" focusable="false">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                    </svg>
                </flux:badge>
                <div class="ml-4">
                    <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-200 sm:text-base">Pending Users</h4>
                    <p class="text-gray-500 dark:text-gray-400 text-sm sm:text-xs">
                        {{ \App\Models\User::where('status', 'Pending')->count() }} Pending
                    </p>
                </div>
            </a>

            <!-- Suspended Users -->
            <a href="{{ route('users.index') }}" role="link" tabindex="0"
                aria-label="Suspended users count: {{ \App\Models\User::where('status', 'Suspended')->count() }} users"
                class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700
                      transform transition duration-300 ease-in-out
                      hover:bg-red-50 hover:dark:bg-red-900 hover:shadow-lg hover:-translate-y-1 hover:scale-105
                      focus:outline-none focus:ring-4 focus:ring-red-300 dark:focus:ring-red-600">
                <flux:badge variant="solid" color="red" class="p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-5 sm:w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </flux:badge>
                <div class="ml-4">
                    <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-200 sm:text-base">Suspended Users</h4>
                    <p class="font-bold text-red-500 text-sm sm:text-xs">
                        {{ \App\Models\User::where('status', 'Suspended')->count() }} Suspended
                    </p>
                </div>
            </a>


            <!-- User Roles -->
            <a href="{{ route('roles.index') }}" role="link" tabindex="0"
                aria-label="Total number of roles: {{ \Spatie\Permission\Models\Role::count() }}"
                class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700
          transform transition duration-300 ease-in-out
          hover:bg-purple-50 hover:dark:bg-purple-900 hover:shadow-lg hover:-translate-y-1 hover:scale-105
          focus:outline-none focus:ring-4 focus:ring-purple-300 dark:focus:ring-purple-600">
                <flux:badge variant="solid" color="purple" class="p-3 rounded-full">
                    <!-- Roles Icon (Heroicons solid/users-cog alternative) -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-5 sm:w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z
                     M21 12v2m0 0v2m0-2h2m-2 0h-2" />
                    </svg>
                </flux:badge>
                <div class="ml-4">
                    <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-200 sm:text-base">User Roles</h4>
                    <p class="text-gray-500 dark:text-gray-400 text-sm sm:text-xs">
                        {{ \Spatie\Permission\Models\Role::count() }} Roles
                    </p>
                </div>
            </a>

        </div>
    </div>
</x-layouts.app>
