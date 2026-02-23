<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;

trait HasRoles
{
    public function roles()
    {
        return $this->morphToMany(Role::class , 'user', 'role_user');
    }

    public function permissions()
    {
        return $this->roles->map->permissions->flatten()->unique('slug');
    }

    public function hasRole(string $role): bool
    {
        return $this->roles->where('slug', $role)->isNotEmpty();
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->hasRole('super-admin')) {
            return true;
        }
        return $this->permissions()->where('slug', $permission)->isNotEmpty();
    }

    public function assignRole(string $role)
    {
        $role = Role::where('slug', $role)->firstOrFail();
        $this->roles()->syncWithoutDetaching($role);
    }
}
