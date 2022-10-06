<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FFEChangeOrder extends Model
{
    use HasFactory;

    CONST ADD = 'add';
    CONST SUB = 'sub';

    protected $fillable = [
     'type' , 'subcontractor_price' ,
     'notes','ffe_proposal_id'
    ];
}
