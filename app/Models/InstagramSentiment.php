<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstagramSentiment extends Model
{
    use HasFactory;
    
    protected $table = 'instagram_comments_sentiment';

    protected $fillable = ['text', 'sentiment', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}