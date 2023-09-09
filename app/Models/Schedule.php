<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

      protected $fillable = [
     'text' , 'start_date' ,'end_date',
     'rec_type' , 'event_length' ,'event_pid',
     'project_id'
    ];

    public function project(){
    	return $this->belongsTo(Project::class);
    }
}
