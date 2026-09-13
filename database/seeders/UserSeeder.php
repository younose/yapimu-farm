<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        activity()->disableLogging();

        $admin = Role::where('name', 'admin')->first();
        $teller = Role::where('name', 'teller')->first();
        $investor = Role::where('name', 'investor')->first();

        $password = Hash::make('password');
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'username' => 'admin',
                'password' => $password,
                'role' => 'admin',
            ],
            [
                'name' => 'Teller',
                'email' => 'teller@example.com',
                'username' => 'teller',
                'password' => $password,
                'role' => 'teller',
            ],
            [
                'name' => 'Investor',
                'email' => 'investor@example.com',
                'username' => 'investor',
                'password' => $password,
                'role' => 'investor',
                'shares' => '10',
            ],
        ];

        foreach ($users as $user) {
            $newUser = User::insert([
                'name' => $user['name'],
                'email' => $user['email'],
                'username' => $user['username'],
                'password' => $user['password'],
            ]);

            $creted = User::where('email', $user['email'])->first();
            if ($user['role'] === 'admin') {
                $creted->assignRole($admin);
            } elseif ($user['role'] === 'teller') {
                $creted->assignRole($teller);
            } elseif ($user['role'] === 'investor') {
                $creted->assignRole($investor);
            }
        }
    }
}
