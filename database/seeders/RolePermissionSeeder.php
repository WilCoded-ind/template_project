<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\Menu;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            ['name' => 'user.view', 'display_name' => 'View Users', 'description' => 'Can view users list'],
            ['name' => 'user.create', 'display_name' => 'Create User', 'description' => 'Can create new user'],
            ['name' => 'user.edit', 'display_name' => 'Edit User', 'description' => 'Can edit user'],
            ['name' => 'user.delete', 'display_name' => 'Delete User', 'description' => 'Can delete user'],

            ['name' => 'role.view', 'display_name' => 'View Roles', 'description' => 'Can view roles list'],
            ['name' => 'role.create', 'display_name' => 'Create Role', 'description' => 'Can create new role'],
            ['name' => 'role.edit', 'display_name' => 'Edit Role', 'description' => 'Can edit role'],
            ['name' => 'role.delete', 'display_name' => 'Delete Role', 'description' => 'Can delete role'],

            ['name' => 'permission.view', 'display_name' => 'View Permissions', 'description' => 'Can view permissions list'],

            ['name' => 'menu.view', 'display_name' => 'View Menus', 'description' => 'Can view menus list'],
            ['name' => 'menu.create', 'display_name' => 'Create Menu', 'description' => 'Can create new menu'],
            ['name' => 'menu.edit', 'display_name' => 'Edit Menu', 'description' => 'Can edit menu'],
            ['name' => 'menu.delete', 'display_name' => 'Delete Menu', 'description' => 'Can delete menu'],

            ['name' => 'permission.create', 'display_name' => 'Create Permission', 'description' => 'Can create new permission'],
            ['name' => 'permission.edit', 'display_name' => 'Edit Permission', 'description' => 'Can edit permission'],
            ['name' => 'permission.delete', 'display_name' => 'Delete Permission', 'description' => 'Can delete permission'],

            ['name' => 'dashboard.view', 'display_name' => 'View Dashboard', 'description' => 'Can view dashboard'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create Roles
        $adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Full system access'
        ]);

        $userRole = Role::create([
            'name' => 'user',
            'display_name' => 'Regular User',
            'description' => 'Limited system access'
        ]);

        // Assign all permissions to admin
        $adminRole->permissions()->attach(Permission::all());

        // Assign only dashboard permission to user
        $userRole->permissions()->attach(Permission::where('name', 'dashboard.view')->first());

        // Create Admin User
        $admin = User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'password' => bcrypt('admin123'),
            'is_active' => true
        ]);
        $admin->roles()->attach($adminRole);

        // Create Regular User
        $user = User::create([
            'name' => 'Regular User',
            'username' => 'user',
            'password' => bcrypt('user123'),
            'is_active' => true
        ]);
        $user->roles()->attach($userRole);

        // Create Default Menus
        $dashboardPermission = Permission::where('name', 'dashboard.view')->first();
        $userViewPermission = Permission::where('name', 'user.view')->first();
        $roleViewPermission = Permission::where('name', 'role.view')->first();
        $menuViewPermission = Permission::where('name', 'menu.view')->first();

        // Dashboard Menu
        Menu::create([
            'name' => 'dashboard',
            'display_name' => 'Dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'route' => 'dashboard',
            'url' => null,
            'parent_id' => null,
            'order' => 1,
            'is_active' => true,
            'permission_id' => $dashboardPermission->id
        ]);

        // User Management Parent Menu
        $userManagement = Menu::create([
            'name' => 'user-management',
            'display_name' => 'User Management',
            'icon' => 'fas fa-users-cog',
            'route' => null,
            'url' => null,
            'parent_id' => null,
            'order' => 2,
            'is_active' => true,
            'permission_id' => $userViewPermission->id
        ]);

        // Users Sub Menu
        Menu::create([
            'name' => 'users',
            'display_name' => 'Users',
            'icon' => 'fas fa-users',
            'route' => 'users.index',
            'url' => null,
            'parent_id' => $userManagement->id,
            'order' => 1,
            'is_active' => true,
            'permission_id' => $userViewPermission->id
        ]);

        // Roles Sub Menu
        Menu::create([
            'name' => 'roles',
            'display_name' => 'Roles',
            'icon' => 'fas fa-user-tag',
            'route' => 'roles.index',
            'url' => null,
            'parent_id' => $userManagement->id,
            'order' => 2,
            'is_active' => true,
            'permission_id' => $roleViewPermission->id
        ]);

        // Menu Management
        Menu::create([
            'name' => 'menus',
            'display_name' => 'Menu Management',
            'icon' => 'fas fa-bars',
            'route' => 'menus.index',
            'url' => null,
            'parent_id' => null,
            'order' => 3,
            'is_active' => true,
            'permission_id' => $menuViewPermission->id
        ]);
    }
}
