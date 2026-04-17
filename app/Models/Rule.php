<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    protected $fillable = [
        'user_id',
        'keyword',
        'category',
        'type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}