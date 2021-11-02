<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeOrder extends Model
{
    use HasFactory;

    CONST ADD = 'add';
    CONST SUB = 'sub';

    protected $fillable = [
     'type' , 'subcontractor_price' ,
     'notes','proposal_id'
    ];

    // public function proposal(){
    //     return $this->hasOne(Proposal::class);
    // }

}
