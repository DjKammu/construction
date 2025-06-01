<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    use HasFactory;

    CONST PASSED = 'Passed';
    CONST FAILED = 'Failed';

    protected $perPage = 20;

    protected $fillable = [
     'project_id' , 'date' ,'inspection_category_id',
     'inspection_type_id' , 'files','notes','passed'
    ];

     public function project(){
        return $this->belongsTo(Project::class);
    }


    public function inspection_category(){
        return $this->belongsTo(InspectionCategory::class,'inspection_category_id');
    }

    public function inspection_type(){
        return $this->belongsTo(InspectionType::class,'inspection_type_id');
    }
}
