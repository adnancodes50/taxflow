<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'upload_id',
        'business_name',
        'file_name',
        'status',
        'payment_status',
        'page_count',
        'price',
            'openai_id', // ✅ ADD THIS

        'income',
        'expenses',
        'net_income',
        'income_categories',
        'expense_categories',
        'analysis_results',
        'classified_results',
        'date_range'
    ];

    protected $casts = [
        'income_categories' => 'array',
        'expense_categories' => 'array',
        'analysis_results' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function upload()
    {
        return $this->belongsTo(Upload::class);
    }
}
