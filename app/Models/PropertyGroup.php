<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyGroup extends Model
{
    use HasFactory;

    protected $fillable = [
     'name' , 'slug' ,'account_number'
    ];

    public function properties(){
    	return $this->hasMany(PropertyType::class);
    } 

}
