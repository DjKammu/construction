<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spreadsheet extends Model
{
    use HasFactory;


       protected $fillable = [
     'state' ,
     'project_id'
    ];

    public function project(){
    	return $this->belongsTo(Project::class);
    }
    
}
