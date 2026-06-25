<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\UserPhotos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserPhotosForm extends Form
{
    public ?UserPhotos $userPhoto = null;

    #[Validate('required|image|mimes:jpg,jpeg,png|max:5120')]
    public $file_photo;

    public function store()
    {
        $this->validate();

        $path = $this->file_photo->store('uploads', 'public');

        UserPhotos::create([
            'user_id' => Auth::id(),
            'file_photo' => $path,
            'upload_time' => now(),
        ]);

        $this->reset('file_photo');
    }

    public function setUserPhoto(UserPhotos $userPhoto)
    {
        $this->userPhoto = $userPhoto;
    }

    public function update()
    {
        $this->validate();

        if ($this->userPhoto->file_photo && Storage::disk('public')->exists($this->userPhoto->file_photo)) {
            Storage::disk('public')->delete($this->userPhoto->file_photo);
        }

        $path = $this->file_photo->store('uploads', 'public');

        $this->userPhoto->update([
            'file_photo' => $path,
            'upload_time' => now(),
        ]);

        $this->reset('file_photo', 'userPhoto');
    }
}