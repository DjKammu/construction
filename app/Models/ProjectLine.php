<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectLine extends Model
{
    use HasFactory;

    protected $perPage = 9;

    protected $fillable = [
     'project_id' , 'description' ,'value',
     'retainage', 'account_number'
    ];

    public function trades(){
        return $this->belongsToMany(Trade::class)->withTimestamps();
    }


    
}
