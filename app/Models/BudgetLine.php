<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetLine extends Model
{
    use HasFactory;

    protected $perPage = 9;

    protected $fillable = [
     'project_id' , 'account_number','trade' ,
     'price_sq_ft','budget'
    ];

     public function project(){
        return $this->belongsTo(Project::class);
    }


}
