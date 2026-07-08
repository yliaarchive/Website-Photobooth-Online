<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');


// ==========================
// ROUTE YANG BISA DIAKSES SEMUA USER
// ==========================
Route::middleware(['auth', 'verified'])->group(function () {

    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('PhotoboxResults', 'pages::photoboxresults.index')
        ->name('photoboxresults.index');

    Route::livewire('Downloads', 'pages::downloads.index')
        ->name('downloads.index');
});


// ==========================
// KHUSUS ADMIN
// ==========================
Route::middleware(['auth', 'verified', 'admin'])->group(function () {

    Route::livewire('FrameCategories', 'pages::framecategories.index')
        ->name('framecategories.index');

    Route::livewire('PhotoFrames', 'pages::photoframes.index')
        ->name('photoframes.index');

    Route::livewire('UserPhotos', 'pages::userphotos.index')
        ->name('userphotos.index');
});

require __DIR__.'/settings.php';