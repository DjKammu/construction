<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FFEVendor extends Model
{
    use HasFactory;

    CONST FFES = 'ffes';
    CONST VENDORS = 'vendors';

    protected $perPage = 9;

    protected $fillable = [
     'name' , 'slug' ,'city',
     'state' ,'zip' , 'email' , 
     'contact_name' ,'photo','notes'
    ];


    public function trades(){
    	return $this->belongsToMany(FFETrade::class)->withTimestamps();
    } 

    // public function proposals(){
    //     return $this->hasMany(Proposal::class);
    // }

    // public function ffe_proposals(){
    //     return $this->hasMany(FFEProposal::class);
    // }

}
