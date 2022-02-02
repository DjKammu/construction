<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeOrderApplicationLine extends Model
{
    use HasFactory;
     
    protected $fillable = [
     'change_order_application_id' ,'billed_to_date',
     'stored_to_date', 'work_completed', 'materials_stored',
     'app_no'
    ];

    // public function project_line(){
    //     return $this->belongsTo(ProjectLine::class);
    // } 

}
