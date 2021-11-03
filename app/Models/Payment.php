<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
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

    protected $fillable = [
     'proposal_id' , 'subcontractor_id' ,'project_id',
     'trade_id' , 'vendor_id' ,'payment_amount', 'date',
     'total_amount' ,'notes','file','status'
    ];

    public static $statusArr = [
       self::DEPOSIT_PAID_STATUS     => self::DEPOSIT_PAID_TEXT,
       self::PROGRESS_PAYMENT_STATUS => self::PROGRESS_PAYMENT_TEXT,
       self::RETAINAGE_PAID_STATUS   => self::RETAINAGE_PAID_TEXT,
       self::FINAL_PAYMENT_STATUS    => self::FINAL_PAYMENT_TEXT
    ];
    

    public function subcontractor(){
        return $this->belongsTo(Subcontractor::class);
    }

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function trade(){
        return $this->belongsTo(Trade::class);
    }



}
