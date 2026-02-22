<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\FavoritesList;

Route::get('/', Dashboard::class)->name('dashboard');

Route::get('/favorites', FavoritesList::class)->name('favorites');
