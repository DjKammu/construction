<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationLine extends Model
{
    use HasFactory;

    protected $perPage = 9;

    protected $fillable = [
     'application_id' , 'project_line_id' ,'billed_to_date',
     'stored_to_date', 'work_completed', 'materials_stored'
    ];

    public function project_line(){
        return $this->belongsTo(ProjectLine::class);
    }

}
