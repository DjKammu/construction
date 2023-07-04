<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftCostProposal extends Model
{
    use HasFactory;

    CONST AWARDED   = 1;
    CONST RETRACTED = 0;
    CONST AWARDED_TEXT = 'Awarded';
    CONST RETRACTED_TEXT = 'Tracted';
    
    protected $perPage = 20;

    protected $fillable = [
     'soft_cost_vendor_id', 'labour_cost',
     'material','subcontractor_price',
     'notes','project_id', 'soft_cost_trade_id', 
     'files','awarded','trade_budget'
    ];


    public function scopeHaveProposal($query, $project_id, $trade_id)
    {
        return $query->where([
         ['project_id',$project_id],
         ['soft_cost_trade_id', $trade_id]
        ]);
    }
    
    public function scopeTrade($query, $trade_id)
    {
        return $query->where(
         ['soft_cost_trade_id' => $trade_id]);
    }
    

    public function scopeIsAwarded($query)
    {
        return $query->where(['awarded' => self::AWARDED]);
    }

    public function vendor(){
        return $this->belongsTo(SoftCostVendor::class,'soft_cost_vendor_id');
    }

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function trade(){
        return $this->belongsTo(SoftCostTrade::class,'soft_cost_trade_id');
    }

    public function changeOrders(){
        return $this->hasMany(SoftCostChangeOrder::class,'soft_cost_proposal_id');
    }

    public function payment(){
         return $this->hasMany(SoftCostPayment::class,'soft_cost_proposal_id');
    }
}
