<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FFEProcurementLog extends Model
{
    use HasFactory;
    
    protected $fillable = [
     'date'  ,'item','project_id' , 
     'ffe_vendor_id' ,'ffe_subcontractor_id', 
     'po_sent','lead_time' ,'status_id','date_shipped',
     'tentative_date_delivery','date_received',
     'store_place','received_shipment_attachment',
     'notes'
    ];

    public function project(){
        return $this->belongsTo(Project::class);
    }

    // public function trade(){
    //     return $this->belongsTo(FFETrade::class,'f_f_e_trade_id');
    // }


    public function vendor(){
        return $this->belongsTo(FFEVendor::class,'ffe_vendor_id');
    }

    public function status(){
        return $this->belongsTo(PaymentStatus::class,'status_id');
    }
}
