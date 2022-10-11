<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RFISubmittalStatus extends Model
{
    use HasFactory;
    protected $table = 'rfi_submittal_statuses';
    
    protected $fillable = [
     'name' , 'slug' ,'account_number'
    ];
}
