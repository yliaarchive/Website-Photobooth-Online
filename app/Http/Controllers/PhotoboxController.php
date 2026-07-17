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
    public function create($frame_id)
    {
        $frame = PhotoFrames::findOrFail($frame_id);
        return view('frontend.create-photobox', compact('frame'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'frame_id' => 'required|exists:photo_frames,id',
            'image_base64' => 'required|string',
        ]);

        $image_parts = explode(";base64,", $request->image_base64);
        $image_base64 = base64_decode($image_parts[1]);
        
        $fileName = 'photobox-results/' . time() . '_' . Str::random(10) . '.png';
        
        Storage::disk('public')->put($fileName, $image_base64);

        PhotoboxResults::create([
            'user_id' => Auth::id(),
            'frame_id' => $request->frame_id,
            'result_file' => $fileName,
        ]);

        return response()->json([
            'success' => true,
            'redirect_url' => route('gallery') 
        ]);
    }
}