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
}
