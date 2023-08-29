<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $perPage = 9;

    protected $fillable = [
     'text' , 'duration','progress',
     'start_date','sortorder','project_id','parent','type'
    ];

     public function project(){
        return $this->belongsTo(Project::class);
    }
}
