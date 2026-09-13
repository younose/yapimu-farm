<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        activity()->disableLogging();

        $admin = Role::create(['name' => 'admin']);
        $teller = Role::create(['name' => 'teller']);
        $investor = Role::create(['name' => 'investor']);
    }
}
