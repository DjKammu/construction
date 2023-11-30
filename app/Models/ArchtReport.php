<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchtReport extends Model
{
    use HasFactory;

    protected $perPage = 20;

    protected $fillable = [
     'file' , 'file_name' , 'application_id'
    ];

    public function application(){
        return $this->belongsTo(Application::class);
    }
   
}
