<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftCostChangeOrder extends Model
{
    use HasFactory;

    use HasFactory;

    CONST ADD = 'add';
    CONST SUB = 'sub';

    protected $fillable = [
     'type' , 'subcontractor_price' ,
     'notes','soft_cost_proposal_id'
    ];
}
