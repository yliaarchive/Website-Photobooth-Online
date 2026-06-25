<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\PhotoFrames;
use Illuminate\Support\Facades\Storage;

class PhotoFrameForm extends Form
{
    public ?PhotoFrames $photoFrame = null;

    #[Validate('required|string|max:100')]
    public $nama_frame;

    #[Validate('nullable|string|max:100')]
    public $tema;

    #[Validate('required|exists:frame_categories,id')]
    public $category_id;

    #[Validate('nullable|image|mimes:png|max:5120')]
    public $gambar_frame;

    public function store()
    {
        $this->validate();

        $path = null;
        if ($this->gambar_frame) {
            $path = $this->gambar_frame->store('frames', 'public');
        }

        PhotoFrames::create([
            'nama_frame' => $this->nama_frame,
            'tema' => $this->tema,
            'category_id' => $this->category_id,
            'gambar_frame' => $path,
        ]);

        $this->reset();
    }

    public function setPhotoFrame(PhotoFrames $photoFrame)
    {
        $this->photoFrame = $photoFrame;
        $this->nama_frame = $photoFrame->nama_frame;
        $this->tema = $photoFrame->tema;
        $this->category_id = $photoFrame->category_id;
    }

    public function update()
    {
        $this->validate([
            'nama_frame' => 'required|string|max:100',
            'tema' => 'nullable|string|max:100',
            'category_id' => 'required|exists:frame_categories,id',
            'gambar_frame' => 'nullable|image|mimes:png|max:5120',
        ]);

        $path = $this->photoFrame->gambar_frame;

        if ($this->gambar_frame) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            $path = $this->gambar_frame->store('frames', 'public');
        }

        $this->photoFrame->update([
            'nama_frame' => $this->nama_frame,
            'tema' => $this->tema,
            'category_id' => $this->category_id,
            'gambar_frame' => $path,
        ]);

        $this->reset();
    }
}