<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeOrderApplication extends Model
{
    use HasFactory;

    protected $fillable = [
     'project_id' , 'description' ,
     'retainage','value' , 'app' 
    ];

    public function application_lines(){
        return $this->hasMany(ChangeOrderApplicationLine::class);
    }

}
