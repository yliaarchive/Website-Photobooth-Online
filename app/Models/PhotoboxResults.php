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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function frame()
    {
        return $this->belongsTo(PhotoFrames::class, 'frame_id');
    }
}