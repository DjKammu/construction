<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ITBTracker extends Model
{
    use HasFactory;
    
    CONST TRUE = 1;
    CONST FALSE = 0;   

    CONST TRUE_TEXT = 'Yes';
    CONST FALSE_TEXT = 'No' ;
    
    protected $perPage = 20;

    protected $fillable = [
     'name' , 'slug' ,
     'account_number',
     'category_id',
     'scope'
    ];

    public static $ITBArr = [
       self::TRUE     => self::TRUE_TEXT,
       self::FALSE   => self::FALSE_TEXT
    ];


}
