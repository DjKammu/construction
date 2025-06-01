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
    CONST INSPECTION    = 'Inspection';
    CONST RECEIVED_SHIPMENT  = 'Received Shipment';
    CONST PROJECT_BUDGET     = 'Project Budget';
    CONST PURCHASE_ORDER     = 'Purchase Order';
    CONST ARCHT_REPORTS      = 'Archt. Reports';

    protected $perPage = 20;

    protected $fillable = [
     'name' , 'slug' ,'account_number'
    ];

    public static $notEditable = [
      self::BID, self::INVOICE, 
      self::LIEN_RELEASE, self::RFI, 
      self::SUBMITTAL, self::PROJECT_BUDGET,
      self::BILL,self::RECEIVED_SHIPMENT,
      self::PURCHASE_ORDER, self::ARCHT_REPORTS,
      self::INSPECTION
    ];

     public function documents(){
    	return $this->hasMany(Document::class);
    }
}
