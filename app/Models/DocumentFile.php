<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentFile extends Model
{
    use HasFactory;
    
    protected $perPage = 60;

   protected $fillable = [
	     'file','name',
	     'date','month',
	     'year'
	 ];

    public function document(){
    	return $this->belongsTo(Document::class);
    }

    public function scopeDocIds($query,$ids){
         return $query->whereIn('document_id',$ids);
    }

}
