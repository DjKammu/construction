<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcontractor extends Model
{
    use HasFactory;
   
    CONST SUBCONTRACTORS = 'subcontractors';

    protected $perPage = 9;

    protected $fillable = [
     'name' , 'slug' ,'city',
     'state' ,'zip' , 'email_1' ,'email_2',
     'email_3' , 'contact_name' ,'office_phone' , 
     'mobile' ,'image','notes'
    ];


    public function trades(){
    	return $this->belongsToMany(Trade::class)->withTimestamps();
    } 


    public function proposals(){
        return $this->hasMany(Proposal::class);
    }

    public function ffe_proposals(){
        return $this->hasMany(FFEProposal::class);
    }
}
