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
     CONST PROJECT   = 'project';
     CONST RFIS       = 'rfis';
     CONST SUBMITTALS  = 'submittals';
     CONST ATTACHMENTS  = 'attachments';
     CONST LIEN_RELEASES  = 'lien-releases';

     protected $fillable = [
     'name' , 'slug' ,'account_number',
     'file','project_id',
     'document_type_id','vendor_id',
     'subcontractor_id', 'proposal_id',
     'payment_id','rfi_id','submittal_id'
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
