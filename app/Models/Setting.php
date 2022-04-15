<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

     protected $fillable = [
     'server_type' ,'server_name',
     'port' ,'user_name',
     'mail_encryption',
     'password' ,'from_email'
    ];


    CONST GMAIL = 'gmail';
    CONST YAHOO = 'yahoo_mail';  
    CONST OTHER = 'other';  
    
    CONST SSL = 'ssl';  
    CONST TLS = 'tls';  

    public static $serverTypes = [
       self::GMAIL ,
       self::YAHOO,
       self::OTHER
    ];


}
