<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BallInCourt extends Model
{
    use HasFactory;

    protected $fillable = [
     'name' , 'slug' ,'account_number'
    ];
}
