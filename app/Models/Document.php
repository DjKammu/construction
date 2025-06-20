<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

     CONST ARCHIEVED = 'archived';
     CONST DOCUMENTS = 'documents';
     CONST PROJECTS  = 'projects';
     CONST PROPOSALS = 'proposals';
     CONST INVOICES  = 'invoices';
     CONST BILLS     = 'bills';
     CONST PROJECT   = 'project';
     CONST RFIS          = 'rfis';
     CONST SUBMITTALS    = 'submittals';
     CONST INSPECTIONS   = 'inspections';
     CONST ATTACHMENTS   = 'attachments';
     CONST LIEN_RELEASES = 'lien-releases';
     CONST FFE_PROPOSALS = 'ffe-proposals';
     CONST RECEIVED_SHIPMENTS = 'received-shipments';
     CONST PURCHASE_ORDERS = 'purchase-orders';
     CONST PROJECTS_PURCHASE_ORDERS = 'projects-purchase-orders';
     CONST BILLS_PURCHASE_ORDERS = 'bills-purchase-orders';
     CONST SOFT_COST_PROPOSALS = 'soft-cost-proposals';
     CONST ARCHT_REPORTS = 'archt-reports';
    

     protected $fillable = [
     'name' , 'slug' ,'account_number',
     'file','project_id',
     'document_type_id','vendor_id',
     'subcontractor_id', 'proposal_id',
     'payment_id','rfi_id','submittal_id',
     'ffe_proposal_id','bill_id','ffe_bill_id',
     'log_id','ffe_log_id','ffe_payment_id','soft_cost_proposal_id',
     'soft_cost_payment_id','soft_cost_bill_id','soft_cost_log_id','inspection_id'
    ];

    public function document_type(){

        return $this->belongsTo(DocumentType::class);
    }

    public function project(){
    	return $this->belongsTo(Project::class);
    }

    public function vendor(){
        return $this->belongsTo(Vendor::class);
    }

    public function subcontractor(){
        return $this->belongsTo(Subcontractor::class);
    }
    
    public function proposal(){
        return $this->belongsTo(Proposal::class);
    }

    public function files(){
        return $this->hasMany(DocumentFile::class);
    }

    public function scopeProjectIds($query,$ids){
         return $query->whereIn('project_id',$ids);
    }

    public function scopeIsProposal($query,$id){
         return $query->where('proposal_id',$id);
    }
}
