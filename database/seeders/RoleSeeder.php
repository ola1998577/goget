<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);
        $storeRole = Role::create(['name' => 'store']);
        $companyRole = Role::create(['name' => 'company']);

        // Create permissions for user
Permission::create(['name' => 'view products']);
Permission::create(['name' => 'add to cart']);
Permission::create(['name' => 'add to wishlist']);
Permission::create(['name' => 'place orders']);
Permission::create(['name' => 'delete order']);
Permission::create(['name' => 'answer questions']);
Permission::create(['name' => 'edit profile']);
Permission::create(['name' => 'spin wheel']);
Permission::create(['name' => 'add product review']);

// Create additional permissions for store
Permission::create(['name' => 'add product']);
Permission::create(['name' => 'edit product']);
Permission::create(['name' => 'delete product']);
Permission::create(['name' => 'view orders']);
Permission::create(['name' => 'accept or reject order']);

// Create additional permissions for company
Permission::create(['name' => 'view my orders']);
Permission::create(['name' => 'change order status']);
Permission::create(['name' => 'assign driver']);
Permission::create(['name' => 'add drivers']);
Permission::create(['name' => 'user managment']);



// Assign permissions to 'user' role
$userRole->givePermissionTo([
    'view products',
    'add to cart',
    'add to wishlist',
    'place orders',
    'delete order',
    'answer questions',
    'edit profile',
    'spin wheel',
    'add product review'
]);

// Assign permissions to 'store' role (all user permissions + additional store permissions)
$storeRole->givePermissionTo($userRole->permissions); // inherit user permissions
$storeRole->givePermissionTo([
    'add product',
    'edit product',
    'delete product',
    'view orders',
    'accept or reject order'
]);

// Assign permissions to 'company' role
$companyRole->givePermissionTo([
    'view my orders',
    'change order status',
    'assign driver',
    'add drivers'
]);

// Assign all permissions to 'admin' role
$allPermissions = Permission::all(); // Get all permissions
$adminRole->syncPermissions($allPermissions);

User::create([

    'f_name' => 'super',
    'l_name' => 'admin',
    'email' => 'admin@gmail.com',
    'email_verified_at' => now(),
    'phone'=>'+963852',
    'password' => '$2y$10$rQN3x8mmnv4MMDTjafh2f.6wTYT9qs/pVUJCRtImyYJYIWo3UapDW', // password
])->assignRole('admin');


    }
}
