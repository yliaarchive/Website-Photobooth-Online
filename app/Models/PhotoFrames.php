<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoFrames extends Model
{
    protected $fillable = [
        'nama_frame',
        'tema',
        'category_id',
        'gambar_frame',
    ];

    public function results()
    {
        return $this->hasMany(PhotoboxResults::class, 'frame_id');
    }

    public function frameCategories()
    {
        return $this->belongsTo(FrameCategories::class, 'category_id');
    }

    
}