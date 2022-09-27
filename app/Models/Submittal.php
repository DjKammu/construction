<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submittal extends Model
{
    use HasFactory;

     protected $perPage = 9;

    protected $fillable = [
     'number' , 'name' ,'user_id','project_id',
     'date_sent' , 'date_recieved' ,'assign_to_id', 
     'subject' ,'subcontractor_id','sent_file',
     'recieved_file' ,'ball_in_court_id','status_id','notes'
    ];

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function assign(){
        return $this->belongsTo(Assignee::class,'assign_to_id');
    }

    public function status(){
        return $this->belongsTo(Status::class);
    }

    public function subcontractor(){
        return $this->belongsTo(Subcontractor::class);
    }
}
