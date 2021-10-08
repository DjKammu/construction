<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

	protected $perPage = 9;

	protected $fillable = [
	'name' , 'slug' ,'city',
	'state' ,'zip' , 'email_1' ,'email_2',
	'email_3' , 'contact_name' ,'office_phone' , 
	'mobile' ,'image','notes'
	];

}
