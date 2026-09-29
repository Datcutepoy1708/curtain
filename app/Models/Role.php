<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'role', 'code');
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->code === 'ROLE_ADMIN' || $this->code === 'admin') {
            return true;
        }

        $perms = is_array($this->permissions) ? $this->permissions : [];
        return in_array($permission, $perms);
    }
}
