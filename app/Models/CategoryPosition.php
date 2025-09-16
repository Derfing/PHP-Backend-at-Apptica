<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryPosition extends Model
{
    protected $fillable = ['date', 'category_id', 'position'];
    protected $casts = [
        'date' => 'date',
    ];
}
