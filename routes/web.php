<?php

use Illuminate\Support\Facades\Route;



Route::middleware(['auth', 'verified'])->group(function () {

    Route::view('/dashboard', 'dashboard')
        ->name('dashboard');

    Route::livewire('PhotoboxResults', 'pages::photoboxresults.index')
        ->name('photoboxresults.index');

    Route::livewire('Downloads', 'pages::downloads.index')
        ->name('downloads.index');

    Route::livewire('UserPhotos', 'pages::userphotos.index')
        ->name('userphotos.index');
});


Route::middleware(['auth', 'verified', 'admin'])->group(function () {

    Route::livewire('FrameCategories', 'pages::framecategories.index')
        ->name('framecategories.index');

    Route::livewire('PhotoFrames', 'pages::photoframes.index')
        ->name('photoframes.index');
        

});

require __DIR__.'/settings.php';