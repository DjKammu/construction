<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $perPage = 9;

    protected $fillable = [
     'name' , 'address' ,'city',
     'state' , 'country' ,'zip_code', 
     'notes' ,'photo','project_type_id',
     'start_date' ,'end_date','due_date','plans_url'
    ];

    public function project_type(){
    	return $this->belongsTo(ProjectType::class);
    }

    public function documents(){
    	return $this->hasMany(Document::class);
    }

    public function trades(){
        return $this->belongsToMany(Trade::class)->withTimestamps();
    }
    
    public function proposals(){
        return $this->hasMany(Proposal::class);
    }

    public function payments(){
        return $this->hasMany(Payment::class);
    }

    
}
