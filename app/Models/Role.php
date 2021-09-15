<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    CONST VIEW = 'view';

    CONST ADD = 'add';

    CONST EDIT = 'edit';

    CONST UPDATE = 'update';

    CONST DELETE = 'delete';

    CONST ADD_USERS = 'add_users';


    protected $fillable = [
        'name', 'slug', 'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_users');
    }
    public function hasAccess(string $permission) : bool
    {
        if ($this->hasPermission($permission)){
            return true;
        }

        return false;
    }
    private function hasPermission(string $permission) : bool
    {
        return in_array($permission,explode(',',$this->permissions)) ?? false;
    }

}
