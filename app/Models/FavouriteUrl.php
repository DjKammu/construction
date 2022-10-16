<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavouriteUrl extends Model
{
    use HasFactory;

     protected $fillable = [
     'status' , 'url' ,'user_id'
    ];
}
