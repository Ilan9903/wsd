<?php

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $roles_structure = [
            'admin' => [
                'users' => 'c,v,u,d,fd',
                'clients' => 'c,v,u,d',
                'groups' => 'c,v,u,d,fd',
                'file_or_folders' => 'c,v,u,d,fd,r',
                'tenant_contracts' => 'v',
                'contacts' => 'c,v,u,d,fd',
                'links' => 'c,v,u,d',
                'histories' => 'v',
                'themes' => 'c,v,u,d',
            ],
            'reseller' => [
                'users' => 'c,v,u,d',
                'clients' => 'c,vo,uo',
            ],
        ];

        $except = [];

        $this->createRoles($roles_structure);

        $this->createPermissions($roles_structure, $except);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Role::all()->each(fn ($p) => $p->delete());
        Permission::all()->each(fn ($p) => $p->delete());
    }

    private function permissionsMap(): array
    {
        return [
            'c' => 'create',
            'v' => 'view',
            'u' => 'update',
            'd' => 'delete',
            'r' => fn ($modelClass) => in_array(SoftDeletes::class, array_keys((new ReflectionClass($modelClass))->getTraits())) ? 'restore' : false,
            'fd' => fn ($modelClass) => in_array(SoftDeletes::class, array_keys((new ReflectionClass($modelClass))->getTraits())) ? 'force_delete' : false,
            'vo' => 'view_own',
            'uo' => 'update_own',
            'do' => 'delete_own',
            'ro' => fn ($modelClass) => in_array(SoftDeletes::class, array_keys((new ReflectionClass($modelClass))->getTraits())) ? 'restore_own' : false,
            'fdo' => fn ($modelClass) => in_array(SoftDeletes::class, array_keys((new ReflectionClass($modelClass))->getTraits())) ? 'force_delete_own' : false,
        ];
    }

    protected function createRoles(array $roles_structure): void
    {
        foreach ($roles_structure as $role_name => $permissions) {
            Role::create(['name' => $role_name]);
        }
    }

    protected function createPermissions(array $roles_structure, array $except): void
    {
        $permissions_map = $this->permissionsMap();

        foreach ($roles_structure as $role_name => $permissions) {
            $role = Role::findByName($role_name);

            foreach ($permissions as $entity_name => $permission_values) {
                $this->handleModelPermissions($entity_name, $permissions_map, $except, $permission_values, $role);

            }
        }
    }

    protected function revokePermissions(array $roles_structure, array $except): void
    {
        $permissions_map = $this->permissionsMap();

        foreach ($roles_structure as $role_name => $permissions) {
            $role = Role::findByName($role_name);

            foreach ($permissions as $entity_name => $permission_values) {
                $permissions_map_filtered = $this->getFilteredPermissionsMap($entity_name, $permissions_map, $except);

                foreach (explode(',', $permission_values) as $permission_value) {
                    $role->revokePermissionTo(Permission::findByName($this->formatPermissionName($permissions_map_filtered[$permission_value], $entity_name)));
                }
            }
        }
    }

    private function getFilteredPermissionsMap(int|string $entity_name, array $permissions_map, array $except): array
    {
        if ($entity_name === 'roles') {
            $entity_class = 'Spatie\\Permission\\Models\\Role';
        } else {
            $entity_class = 'App\\Models\\'.Str::of($entity_name)->singular()->camel()->ucfirst();
        }

        $permissions_map_filtered = array_map(fn ($permission_map) => (is_string($permission_map) ? $permission_map : $permission_map($entity_class)), $permissions_map);
        $permissions_map_filtered = array_map(fn ($permission_map) => in_array($this->formatPermissionName($permission_map, $entity_name), $except) ? false : $permission_map, $permissions_map_filtered);
        $permissions_map_filtered = array_filter($permissions_map_filtered);

        return $permissions_map_filtered;
    }

    private function handleModelPermissions(int|string $entity_name, array $permissions_map, array $except, mixed $permission_values, Spatie\Permission\Contracts\Role|Role $role): void
    {
        $permissions_map_filtered = $this->getFilteredPermissionsMap($entity_name, $permissions_map, $except);

        $this->createPermissionsIfDoesntExist($permissions_map_filtered, $entity_name);

        $this->assignModelPermissionsToRole($permission_values, $role, $permissions_map_filtered, $entity_name);
    }

    private function createPermissionsIfDoesntExist(array $permissions_map_filtered, int|string $entity_name): void
    {
        foreach ($permissions_map_filtered as $permission_map) {
            $permissionName = $this->formatPermissionName($permission_map, $entity_name);
            // If permission doesn't already exist, create these
            if (! Permission::where('name', $permissionName)->exists()) {
                Permission::create([
                    'name' => "$permissionName",
                ]);
            }
        }
    }

    private function assignModelPermissionsToRole(mixed $permission_values, Spatie\Permission\Contracts\Role|Role $role, array $permissions_map_filtered, int|string $entity_name): void
    {
        foreach (explode(',', $permission_values) as $permission_value) {
            $role->givePermissionTo(Permission::findByName($this->formatPermissionName($permissions_map_filtered[$permission_value], $entity_name)));
        }
    }

    private function formatPermissionName($permission, int|string $entity_name): string
    {
        return "{$permission}_{$entity_name}";
    }
};
