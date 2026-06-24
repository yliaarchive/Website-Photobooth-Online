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

    // Validasi file: wajib diisi, format gambar, maksimal 5MB
    #[Validate('required|image|mimes:jpg,jpeg,png|max:5120')]
    public $file_photo;

    // Untuk menyimpan data baru
    public function store()
    {
        $this->validate();

        // Simpan file ke folder storage/app/public/uploads
        $path = $this->file_photo->store('uploads', 'public');

        UserPhotos::create([
            'user_id' => Auth::id(),
            'file_photo' => $path,
            'upload_time' => now(),
        ]);

        $this->reset('file_photo');
    }

    // Menyiapkan data untuk edit (opsional jika user mau ganti foto)
    public function setUserPhoto(UserPhotos $userPhoto)
    {
        $this->userPhoto = $userPhoto;
    }

    // Mengupdate foto yang sudah ada
    public function update()
    {
        $this->validate();

        // Hapus foto lama jika ada
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