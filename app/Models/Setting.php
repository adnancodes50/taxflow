<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'stripe_public_key',
        'stripe_secret_key',
        'per_page_price',
        'ai_prompt',
        'ai_key',
    ];
}
