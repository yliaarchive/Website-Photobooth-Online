<?php

namespace App\Http\Controllers;

use App\Models\PhotoFrames;
use App\Models\PhotoboxResults;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoboxController extends Controller
{
    // Fungsi untuk membuka halaman editor
    public function create($frame_id)
    {
        $frame = PhotoFrames::findOrFail($frame_id);
        return view('frontend.create-photobox', compact('frame'));
    }

    // Fungsi BARU untuk menyimpan foto
    public function store(Request $request)
    {
        // Validasi data yang masuk
        $request->validate([
            'frame_id' => 'required|exists:photo_frames,id',
            'image_base64' => 'required|string',
        ]);

        // 1. Memisahkan header "data:image/png;base64," dari data aslinya
        $image_parts = explode(";base64,", $request->image_base64);
        $image_base64 = base64_decode($image_parts[1]);
        
        // 2. Membuat nama file unik
        $fileName = 'photobox-results/' . time() . '_' . Str::random(10) . '.png';
        
        // 3. Menyimpan file fisik ke storage/app/public/photobox-results
        Storage::disk('public')->put($fileName, $image_base64);

        // 4. Menyimpan data ke database
        PhotoboxResults::create([
            'user_id' => Auth::id(),
            'frame_id' => $request->frame_id,
            'result_file' => $fileName,
        ]);

        // 5. Berikan respon sukses untuk ditangkap oleh Javascript
        return response()->json([
            'success' => true,
            'redirect_url' => route('gallery') // Arahkan otomatis ke halaman Galeri
        ]);
    }
}