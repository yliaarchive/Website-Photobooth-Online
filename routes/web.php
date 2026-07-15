<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhotoboxController;

Route::get('/', function () {
    return view('frontend.home');
})->name('home');
Route::get('/frames', function () {
    return view('frontend.frames'); 
})->name('frames');
Route::get('/create/{frame}', function ($frame) {

    $frame = PhotoFrames::findOrFail($frame);

    return view('frontend.create', compact('frame'));

})->name('frontend.create');
Route::get('/gallery', function () {
    $results = \App\Models\PhotoboxResults::latest()->paginate(12);
    return view('frontend.gallery', compact('results'));
})->name('gallery');
Route::get('/create-photobox/{frame_id}', [PhotoboxController::class, 'create'])
    ->name('photobox.create')
    ->middleware('auth');
Route::post('/store-photobox', [App\Http\Controllers\PhotoboxController::class, 'store'])
    ->name('photobox.store')
    ->middleware('auth');

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