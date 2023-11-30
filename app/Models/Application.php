<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

      protected $perPage = 9;

    protected $fillable = [
     'project_id' , 'application_date' ,'period_to'
    ];

    public function application_lines(){
        return $this->hasMany(ApplicationLine::class);
    }

    public function archt_reports(){
        return $this->hasMany(ArchtReport::class);
    }

}
