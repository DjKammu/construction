<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetLine extends Model
{
    use HasFactory;

    protected $perPage = 9;

    protected $fillable = [
     'project_id' , 'account_number','trade',
     'price_sq_ft','budget'
    ];

     public function project(){
        return $this->belongsTo(Project::class);
    }

    public function scopeUpdateSqFt($query){
         $total_construction_sq_ft = request()->total_construction_sq_ft;
         $query->update([
          'price_sq_ft' => \DB::raw( "budget/$total_construction_sq_ft")
         ]);
    	 
    }

    public function getPriceSqFtAttribute($value){
        return $value ? number_format($value,2) : 0.00;
    }


}
