<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FFEPayment extends Model
{
    use HasFactory;

    CONST DEPOSIT_PAID_TEXT     = 'Deposit Paid';
    CONST PROGRESS_PAYMENT_TEXT = 'Progress Payment';
    CONST RETAINAGE_PAID_TEXT   = 'Retainage Paid';
    CONST FINAL_PAYMENT_TEXT    = 'Final Payment';

    CONST DEPOSIT_PAID_STATUS     = 'deposit';
    CONST PROGRESS_PAYMENT_STATUS = 'progress';
    CONST RETAINAGE_PAID_STATUS   = 'retainage';
    CONST FINAL_PAYMENT_STATUS    = 'final';
    
    CONST VENDOR = 'vendor';
    CONST SUBCONTRACTOR = 'subcontractor';
    
    protected $fillable = [
     'ffe_proposal_id'  ,'project_id',
     'f_f_e_trade_id' , 'f_f_e_vendor_id' ,'payment_amount', 'date',
     'total_amount' ,'notes','file','status','invoice_number',
     'unconditional_lien_release_file','conditional_lien_release_file',
     'non_contract','ffe_bill_id','purchase_order'
    ];

    public static $statusArr = [
       self::DEPOSIT_PAID_STATUS     => self::DEPOSIT_PAID_TEXT,
       self::PROGRESS_PAYMENT_STATUS => self::PROGRESS_PAYMENT_TEXT,
       self::RETAINAGE_PAID_STATUS   => self::RETAINAGE_PAID_TEXT,
       self::FINAL_PAYMENT_STATUS    => self::FINAL_PAYMENT_TEXT
    ];
    

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function trade(){
        return $this->belongsTo(FFETrade::class,'f_f_e_trade_id');
    }

    public function proposal(){
        return $this->belongsTo(FFEProposal::class,'ffe_proposal_id');
    }

    public function vendor(){
        return $this->belongsTo(FFEVendor::class,'f_f_e_vendor_id');
    }

    public function material(){
        return $this->belongsTo(VendorMaterial::class);
    }
    
    public static function format($num){
        return number_format($num, 2, '.', ',');
        return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
    }


}
