<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiInstagram extends Model
{
    use HasFactory;

    protected $table = 'api_instagram';

    protected $fillable = ['app_id', 'app_secret', 'user_id', 'page_id', 'file', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}