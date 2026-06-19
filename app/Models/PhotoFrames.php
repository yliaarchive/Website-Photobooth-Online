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
}
