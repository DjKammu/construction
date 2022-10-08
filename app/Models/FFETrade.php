<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FFETrade extends Model
{
    use HasFactory;

    CONST FEE_TRADES = 'ffe-trades';
    
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

    public function ffe_vendors(){
        return $this->belongsToMany(FFEVendor::class)->withTimestamps();
    }

    public function category(){
        return $this->hasOne(Category::class);
    }
}
