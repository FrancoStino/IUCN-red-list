<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\FavoritesList;
use App\Livewire\AssessmentsList;
use App\Livewire\SpeciesDetail;
use App\Livewire\AssessmentDetail;

Route::get('/', Dashboard::class)->name('dashboard');
Route::get('/favorites', FavoritesList::class)->name('favorites');
Route::get('/assessments/{type}/{code}', AssessmentsList::class)
    ->where('type', 'system|country')
    ->name('assessments');

Route::get('/species/{sisId}', SpeciesDetail::class)->name('species.detail');
Route::get('/assessment/{assessmentId}', AssessmentDetail::class)->name('assessment.detail');
