<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

    CONST BID     = 'Bid';
    CONST INVOICE = 'Invoice';
    CONST BILL    = 'Bill';
    CONST LIEN_RELEASE  = 'Lien Release';
    CONST RFI           = 'RFI';
    CONST SUBMITTAL     = 'Submittal';
    CONST RECEIVED_SHIPMENT  = 'Received Shipment';
    CONST PROJECT_BUDGET     = 'Project Budget';

    protected $perPage = 20;

    protected $fillable = [
     'name' , 'slug' ,'account_number'
    ];

    public static $notEditable = [
      self::BID, self::INVOICE, 
      self::LIEN_RELEASE, self::RFI, 
      self::SUBMITTAL, self::PROJECT_BUDGET,
      self::BILL,self::RECEIVED_SHIPMENT
    ];

     public function documents(){
    	return $this->hasMany(Document::class);
    }
}
