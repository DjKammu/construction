<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftCostVendor extends Model
{
    use HasFactory;

    CONST SOFT_COSTS = 'soft-costs';
    CONST VENDORS = 'vendors';

    protected $perPage = 9;

    protected $fillable = [
     'name' , 'slug' ,'city','address',
     'state' ,'zip' , 'email' , 
     'contact_name' ,'photo','notes'
    ];


    public function trades(){
    	return $this->belongsToMany(SoftCostTrade::class)->withTimestamps();
    } 

    public function sc_proposals(){
        return $this->hasMany(SoftCostProposal::class);
    }


}
