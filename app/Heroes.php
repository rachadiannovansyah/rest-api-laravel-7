<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Heroes extends Model
{
    protected $table = 'heroes';

    protected $fillable = ['name', 'ability', 'description'];
}
