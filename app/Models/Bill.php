<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    CONST DEPOSIT_PAID_TEXT     = 'Deposit Paid';
    CONST PROGRESS_PAYMENT_TEXT = 'Progress Payment';
    CONST RETAINAGE_PAID_TEXT   = 'Retainage Paid';
    CONST FINAL_PAYMENT_TEXT    = 'Final Payment';
    CONST PAID_BILL_TEXT        = 'Paid';
    CONST UNPAID_BILL_TEXT      = 'Unpaid';

    CONST DEPOSIT_PAID_STATUS     = 'deposit';
    CONST PROGRESS_PAYMENT_STATUS = 'progress';
    CONST RETAINAGE_PAID_STATUS   = 'retainage';
    CONST FINAL_PAYMENT_STATUS    = 'final';

    CONST PAID_BILL_STATUS      = 'paid';
    CONST UNPAID_BILL_STATUS    = 'unpaid';
    
    CONST VENDOR = 'vendor';
    CONST SUBCONTRACTOR = 'subcontractor';
    
    protected $fillable = [
     'proposal_id' , 'subcontractor_id' ,'project_id',
     'trade_id' , 'vendor_id' ,'payment_amount', 'date',
     'total_amount' ,'notes','file','status','invoice_number','material_id',
     'bill_status','payment_id','assigned_to','total_subcontractor_payment',
     'retainage_percentage','retainage_held','purchase_order'
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

    public function proposal(){
        return $this->belongsTo(Proposal::class);
    }

    public function vendor(){
        return $this->belongsTo(Vendor::class);
    }

    public function material(){
        return $this->belongsTo(VendorMaterial::class);
    }
    
    public static function format($num){
        return number_format($num, 2, '.', ',');
        return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
    }
}
