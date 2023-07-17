<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $perPage = 9;

    CONST ACTIVE_TEXT       = 'Active';
    CONST PUT_ON_HOLD_TEXT  = 'Put On Hold';
    CONST FINISHED_TEXT     = 'Finished';
    CONST CANCELLED_TEXT    = 'Cancelled';
    CONST ARCHIVED_TEXT     = 'Archived';

    CONST ACTIVE_STATUS      = 'active';
    CONST PUT_ON_HOLD_STATUS = 'put_on_hold';
    CONST FINISHED_STATUS    = 'finished';
    CONST CANCELLED_STATUS   = 'cancelled';
    CONST ARCHIVED_STATUS    = 'archived';


    protected $fillable = [
     'name' , 'address' ,'city',
     'state' , 'country' ,'zip_code', 
     'notes' ,'photo','project_type_id',
     'start_date' ,'end_date','due_date','plans_url',
     'owner_name' ,'owner_street','owner_city','owner_state',
     'owner_zip' ,'contract_name','contract_street','contract_city',
     'contract_state' ,'contract_zip','architect_name','architect_street',
     'architect_city' ,'architect_state','architect_zip','contract_date',
     'project_date' ,'retainage_percentage','original_amount','project_email',
     'notary_name' ,'notary_country','notary_state','commission_expire_date',
     'status','project_number','contract_phone','property_type_id','attachment',
     'attachment_name','user_id','property_group_id','project_email_user',
     'total_construction_sq_ft','hotel_keys','subcontractor_retainage'
    ];

    
    protected $casts = [
    'start_date' => 'date', 
    'end_date' => 'date'
    ];

    public function project_type(){
    	return $this->belongsTo(ProjectType::class);
    }

    public function documents(){
    	return $this->hasMany(Document::class);
    }

    public function trades(){
        return $this->belongsToMany(Trade::class)->withTimestamps();
    }
    
    public function proposals(){
        return $this->hasMany(Proposal::class);
    }

     public function logs(){
        return $this->hasMany(ProcurementLog::class);
    }

    public function payments(){
        return $this->hasMany(Payment::class);
    }

    public function projectEmailUser(){
        return $this->belongsTo(User::class, 'project_email_user');
    }

     public function ffe_payments(){
        return $this->hasMany(FFEPayment::class);
    } 

    public function bills(){
        return $this->hasMany(Bill::class);
    } 

    public function ffe_bills(){
        return $this->hasMany(FFEBill::class);
    }

     public function rfis(){
        return $this->hasMany(RFI::class);
    } 

    public function submittals(){
        return $this->hasMany(Submittal::class);
    } 

    public function project_lines(){
        return $this->hasMany(ProjectLine::class);
    }
    public function budget_lines(){
        return $this->hasMany(BudgetLine::class);
    }
    
    public function applications(){
        return $this->hasMany(Application::class);
    }
    
    public function changeOrderApplications(){
        return $this->hasMany(ChangeOrderApplication::class);
    }
    
    public function closeProject(){
        return $this->hasOne(CloseProject::class);
    }

    public function p_status(){
        return $this->belongsTo(Status::class,'status');
    }

    public function ffe_proposals(){
        return $this->hasMany(FFEProposal::class);
    }


    public function sc_proposals(){
        return $this->hasMany(SoftCostProposal::class);
    }
    
    public function ffe_logs(){
        return $this->hasMany(FFEProcurementLog::class);
    }

    public function ffe_trades(){
        return $this->belongsToMany(FFETrade::class)->withTimestamps();
    }

    public function sc_trades(){
        return $this->belongsToMany(SoftCostTrade::class)->withTimestamps();
    }
    
    public function propertyGroup(){
        return $this->hasOne(PropertyGroup::class);
    }

    public function property(){
        return $this->belongsTo(PropertyType::class,'property_type_id');
    }

    public function setSubcontractorRetainageAttribute($value){
       $this->attributes['subcontractor_retainage'] = ($value) ?  $value : 10;
    }
    
}
