<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Users\UserManager;
use App\Livewire\Roles\RoleManager;
use App\Livewire\Counties\CountiesManager;
use App\Livewire\Districts\DistrictsManager;
use App\Livewire\Communities\CommunityManager;
use App\Livewire\Blocks\BlockManager;
use App\Livewire\Streets\StreetManager;
use App\Livewire\Leaderships\LeadershipManager;
use App\Livewire\Leaderships\LeaderManager;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::get('/roles', RoleManager::class)->name('roles.index');
    Route::get('/users', UserManager::class)->name('users.index');
    Route::get('/counties', CountiesManager::class)->name('counties.index');
    Route::get('/districts', DistrictsManager::class)->name('districts.index');
    Route::get('/communities', CommunityManager::class)->name('communities.index');
    Route::get('/blocks', BlockManager::class)->name('blocks.index');
    Route::get('/streets', StreetManager::class)->name('streets.index');
    Route::get('/leaderships', LeadershipManager::class)->name('leaderships.index');
    Route::get('/leader-manager', LeaderManager::class)->name('leader.index');
});

require __DIR__ . '/auth.php';
