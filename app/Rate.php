<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    public $timestamps = false;

    protected $fillable = ['data', 'date'];
    protected $casts = ['data' => 'array'];
}
