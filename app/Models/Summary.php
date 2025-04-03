<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Summary extends Model
{
    use HasFactory;

    // Define the fillable fields
    protected $fillable = [
        'user_id',
        'upload_id',
        'summary_text',
    ];

    // Define relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function upload()
    {
        return $this->belongsTo(UserUpload::class);
    }
}

