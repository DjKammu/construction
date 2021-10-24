<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;
    
    CONST PROPOSALS = 'proposals';
    CONST AWARDED   = 1;
    CONST RETRACTED = 0;
    CONST AWARDED_TEXT = 'Awarded';
    CONST RETRACTED_TEXT = 'Tracted';
    
    protected $perPage = 20;

    protected $fillable = [
     'subcontractor_id', 'labour_cost',
     'material','subcontractor_price',
     'notes','project_id', 'trade_id', 
     'files','awarded'
    ];


    public function scopeHaveProposal($query, $project_id, $trade_id)
    {
        return $query->where([
         ['project_id',$project_id],
         ['trade_id', $trade_id]
        ]);
    }
    
    public function scopeTrade($query, $trade_id)
    {
        return $query->where(
         ['trade_id' => $trade_id]);
    }
    

    public function scopeIsAwarded($query)
    {
        return $query->where(['awarded' => self::AWARDED]);
    }

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
