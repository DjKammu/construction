<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;


     protected $fillable = [
     'title' , 'start' ,'end',
     'project_id'
    ];

    public function project(){
    	return $this->belongsTo(Project::class);
    }

}
