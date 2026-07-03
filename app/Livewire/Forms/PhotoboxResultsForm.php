<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\PhotoboxResults;
use App\Models\UserPhotos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoboxResultsForm extends Form
{
    public $frame_id;
    public $final_image_base64; 
    public $photos = []; 

    public function store()
    {
        if (!$this->final_image_base64 || !$this->frame_id) {
            return;
        }

        if (!empty($this->photos)) {
            foreach ($this->photos as $photo) {
                $photoPath = $photo->store('uploads', 'public');
                UserPhotos::create([
                    'user_id' => Auth::id(),
                    'file_photo' => $photoPath,
                    'upload_time' => now(),
                ]);
            }
        }

        $image_parts = explode(";base64,", $this->final_image_base64);
        $image_base64 = base64_decode($image_parts[1]);

        Storage::disk('public')->makeDirectory('results');
        $filename = 'results/photobox_' . Str::random(10) . '_' . time() . '.jpg';
        Storage::disk('public')->put($filename, $image_base64);

        PhotoboxResults::create([
            'user_id' => Auth::id(),
            'frame_id' => $this->frame_id,
            'result_file' => $filename,
        ]);

        $this->reset();
    }
}