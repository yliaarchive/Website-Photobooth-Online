<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\PhotoboxResults;
use App\Models\UserPhotos;
use App\Models\PhotoFrames;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoboxResultsForm extends Form
{
    #[Validate('required|exists:photo_frames,id')]
    public $frame_id;

    // Ubah menjadi array untuk menampung 1-6 foto sekaligus dari perangkat
    #[Validate([
        'photos' => 'required|array|min:1|max:6',
        'photos.*' => 'image|mimes:jpg,jpeg,png|max:5120',
    ])]
    public $photos = [];

    public function store()
    {
        $this->validate();

        $frame = PhotoFrames::find($this->frame_id);
        $framePath = storage_path('app/public/' . $frame->gambar_frame);
        $frameImg = imagecreatefrompng($framePath);
        
        $frameW = imagesx($frameImg);
        $frameH = imagesy($frameImg);

        // Siapkan Kanvas Kosong sebesar Frame
        $finalImg = imagecreatetruecolor($frameW, $frameH);
        imagealphablending($finalImg, true);
        imagesavealpha($finalImg, true);
        $transparent = imagecolorallocatealpha($finalImg, 0, 0, 0, 127);
        imagefill($finalImg, 0, 0, $transparent);

        // Hitung tinggi setiap slot foto (dibagi rata berdasarkan jumlah foto yang diupload)
        // Logika ini mengasumsikan template frame memiliki susunan bolongan vertikal (photostrip)
        $jumlahFoto = count($this->photos);
        $slotHeight = $frameH / $jumlahFoto;
        $currentY = 0;

        // Loop untuk memproses setiap foto yang diunggah
        foreach ($this->photos as $photo) {
            // 1. Simpan foto ke tabel user_photos sebagai "perantara"
            $photoPath = $photo->store('uploads', 'public');
            UserPhotos::create([
                'user_id' => Auth::id(),
                'file_photo' => $photoPath,
                'upload_time' => now(),
            ]);

            // 2. Proses foto untuk dimasukkan ke kanvas
            $fullPath = storage_path('app/public/' . $photoPath);
            $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

            if ($ext === 'png') {
                $tempImg = imagecreatefrompng($fullPath);
            } else {
                $tempImg = imagecreatefromjpeg($fullPath);
            }

            $tempW = imagesx($tempImg);
            $tempH = imagesy($tempImg);

            // 3. Taruh foto ke dalam kanvas pada koordinat Y saat ini
            imagecopyresampled($finalImg, $tempImg, 0, $currentY, 0, 0, $frameW, $slotHeight, $tempW, $tempH);
            
            // Tambah koordinat Y untuk slot foto berikutnya
            $currentY += $slotHeight;
            imagedestroy($tempImg);
        }

        // Tumpuk Frame PNG tepat di atas semua kumpulan foto tadi
        imagecopy($finalImg, $frameImg, 0, 0, 0, 0, $frameW, $frameH);

        // Simpan hasil akhir ke storage
        Storage::disk('public')->makeDirectory('results');
        $filename = 'results/photobox_' . Str::random(10) . '_' . time() . '.png';
        imagepng($finalImg, storage_path('app/public/' . $filename));

        // Bersihkan memori RAM
        imagedestroy($frameImg);
        imagedestroy($finalImg);

        // Catat ke tabel photobox_results
        PhotoboxResults::create([
            'user_id' => Auth::id(),
            'frame_id' => $this->frame_id,
            'result_file' => $filename,
        ]);

        $this->reset();
    }
}
