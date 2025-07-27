<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aspect extends Model
{
    use HasFactory;

    protected $table = 'aspect';

    protected $fillable = [
        'aspect',
        'positif',
        'negatif',
        'netral',
        'total',
        'persentasePositif',
        'persentaseNegatif',
        'persentaseNetral',
    ];
}