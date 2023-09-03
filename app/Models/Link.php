<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
     'source' , 'target','type','project_id'
    ];

     public function project(){
        return $this->belongsTo(Project::class);
    }
}
