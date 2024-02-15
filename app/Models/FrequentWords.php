<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrequentWords extends Model
{
    use HasFactory;
    
    protected $table = 'frequent_words';

    protected $fillable = ['sentiment', 'word', 'frequency'];
}