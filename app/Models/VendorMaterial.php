<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorMaterial extends Model
{
    use HasFactory;

    protected $perPage = 9;

	protected $fillable = [
	'name' , 'account_number'
	];

    public function Vendor(){
        return $this->hasOne(Category::class);
    }
}
