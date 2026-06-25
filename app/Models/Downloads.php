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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function result()
    {
        return $this->belongsTo(PhotoboxResults::class, 'result_id');
    }
}