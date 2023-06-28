<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftCostTrade extends Model
{
    use HasFactory;

    CONST SOFT_COST_TRADES = 'soft-cost-trades';
    
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

    public function sc_vendors(){
        return $this->belongsToMany(SoftCostVendor::class)->withTimestamps();
    }

    public function category(){
        return $this->hasOne(SoftCostCategory::class);
    }

}
