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

    // 👇 UserPhotos dipindah ke sini agar user biasa bisa mengaksesnya 👇
    Route::livewire('UserPhotos', 'pages::userphotos.index')
        ->name('userphotos.index');
});


// ==========================
// KHUSUS ADMIN
// ==========================
Route::middleware(['auth', 'verified', 'admin'])->group(function () {

    Route::livewire('FrameCategories', 'pages::framecategories.index')
        ->name('framecategories.index');

    Route::livewire('PhotoFrames', 'pages::photoframes.index')
        ->name('photoframes.index');
        
    // UserPhotos sudah dihapus dari grup ini
});

require __DIR__.'/settings.php';