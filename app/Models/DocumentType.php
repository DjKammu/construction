<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

    protected $perPage = 20;

    protected $fillable = [
     'name' , 'slug' ,'account_number'
    ];

     public function documents(){
    	return $this->hasMany(Document::class);
    }
}
