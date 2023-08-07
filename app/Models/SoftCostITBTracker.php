<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftCostITBTracker extends Model
{
    use HasFactory;

    CONST TRUE = 1;
    CONST FALSE = 0;   

    CONST TRUE_TEXT = 'Yes';
    CONST FALSE_TEXT = 'No' ;
    
    protected $perPage = 20;

    protected $fillable = [
     'project_id' , 'soft_cost_trade_id' ,
     'soft_cost_vendor_id',
     'mail_sent','bid_recieved',
     'contract_sign'
    ];

    public static $ITBArr = [
       self::TRUE     => self::TRUE_TEXT,
       self::FALSE   => self::FALSE_TEXT
    ];

}
