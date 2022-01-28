<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CloseProject extends Model
{
    use HasFactory;

     protected $fillable = [
     'project_id' , 'application_date' ,'period_to','retainage_value'
    ];
}
