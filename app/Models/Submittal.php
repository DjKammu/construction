<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submittal extends Model
{
    use HasFactory;

     protected $perPage = 9;

    protected $fillable = [
     'number' , 'name' ,'user_id',
     'date_sent' , 'date_recieved' ,'assign_to_id', 
     'subject' ,'subcontractor_id','sent_file',
     'recieved_file' ,'ball_in_court_id','status_id','notes'
    ];
}
