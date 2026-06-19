<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Downloads extends Model
{
    protected $fillable = [
        'user_id',
        'result_id',
        'download_date',
    ];
}
