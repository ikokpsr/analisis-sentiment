<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TotalSentiment extends Model
{
    use HasFactory;

    protected $table = 'result_sentiment';

    protected $fillable = ['sentiment', 'total'];
}