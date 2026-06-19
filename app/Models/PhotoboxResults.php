<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoboxResults extends Model
{
    protected $fillable = [
        'user_id',
        'frame_id',
        'result_file',
    ];
}
