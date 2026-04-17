<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $fillable = [
        'user_id',
        'file_name',
        'file_buffer',
        'file_mime_type',
        'is_guest'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

public function report()
{
    return $this->hasOne(Report::class);
}
}
