<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Rules\NotReservedUsername;

new #[Layout('components.layouts.auth')] class extends Component {

    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $username = '';
    public string $phone = '';
    public $avatar;
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'username' => ['required', 'string', 'min:3', 'max:30', 'unique:' . User::class, 'alpha_dash', 'not_regex:/^\d+$/', new NotReservedUsername()],
            'phone' => ['required', 'digits:10', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,svg'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['avatar'] = $this->avatar->store('avatars', 'public');

        $user = User::create($validated);

        event(new Registered($user));

        Auth::login($user);

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }
};

?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" enctype="multipart/form-data" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name"
            :placeholder="__('Full Name')" />
        <!-- Username  -->
        <flux:input wire:model="username" :label="__('Username')" type="text" required autofocus autocomplete="username"
            :placeholder="__('Enter your username')" />
        <!-- phone -->
        <flux:input wire:model="phone" :label="__('Phone')" type="tel" required maxlength="10" autocomplete="tel"
            :placeholder="__('1234568790 ...')" />

        <!-- Email Address -->
        <flux:input wire:model="email" :label="__('Email address')" type="email" required autocomplete="email"
            placeholder="email@example.com" />

        <!-- Password -->
        <flux:input wire:model="password" :label="__('Password')" type="password" required autocomplete="new-password"
            :placeholder="__('Password')" viewable />

        <!-- Confirm Password -->
        <flux:input wire:model="password_confirmation" :label="__('Confirm password')" type="password" required
            autocomplete="new-password" :placeholder="__('Confirm password')" viewable />

        <flux:input wire:model="avatar" :label="__('Avatar')"  type="file" />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Create account') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('Already have an account?') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>
