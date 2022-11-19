<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementLog extends Model
{
    use HasFactory;



    protected $fillable = [
     'date'  ,'item','project_id' , 
     'vendor_id' ,'subcontractor_id', 
     'po_sent','lead_time' ,'status_id','date_shipped',
     'tentative_date_delivery','date_received',
     'store_place','received_shipment_attachment',
     'notes','procurement_status_id','invoice',
     'po_sent_file'
    ];

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function vendor(){
        return $this->belongsTo(Vendor::class,'vendor_id');
    }
    
    public function subcontractor(){
        return $this->belongsTo(Subcontractor::class,'subcontractor_id');
    }

    public function status(){
        return $this->belongsTo(PaymentStatus::class,'status_id');
    }
}
