<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('FrameCategories', 'pages::framecategories.index')->name('framecategories.index');
    Route::livewire('PhotoFrame', 'pages::photoframe.index')->name('photoframe.index');
    Route::livewire('UserPhotos', 'pages::userphotos.index')->name('userphotos.index');
});

require __DIR__.'/settings.php';
