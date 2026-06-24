<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPhotos extends Model
{
    protected $fillable = [
        'user_id',
        'file_photo',
        'upload_time',
    ];

    // Tambahkan relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}