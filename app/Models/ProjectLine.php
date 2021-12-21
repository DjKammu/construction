<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectLine extends Model
{
    use HasFactory;

    protected $perPage = 9;

    protected $fillable = [
     'project_id' , 'description' ,'value',
     'retainage' 
    ];


    // public function project_type(){
    // 	return $this->belongsTo(ProjectType::class);
    // }

    // public function documents(){
    // 	return $this->hasMany(Document::class);
    // }

    public function trades(){
        return $this->belongsToMany(Trade::class)->withTimestamps();
    }
    
    // public function proposals(){
    //     return $this->hasMany(Proposal::class);
    // }

    // public function payments(){
    //     return $this->hasMany(Payment::class);
    // }

    
}
