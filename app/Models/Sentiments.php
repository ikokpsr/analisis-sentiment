<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sentiments extends Model
{
    use HasFactory;

    protected $table = 'sentiments';

    protected $fillable = ['teks', 'sentiment'];
}