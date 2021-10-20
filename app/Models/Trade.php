<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;
    
    CONST TRADES = 'trades';
    
    protected $perPage = 20;

    protected $fillable = [
     'name' , 'slug' ,
     'account_number',
     'category_id',
     'scope'
    ];


    public function projects(){
        return $this->belongsToMany(Project::class)->withTimestamps();
    }

}
