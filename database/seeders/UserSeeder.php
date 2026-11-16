<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Demo accounts with documented credentials:
     * - Admin:     admin@cargotrace.test     / Password123!
     * - Operator:  operator@cargotrace.test  / Password123!
     * - Customer1: customer1@cargotrace.test / Password123!
     * - Customer2: customer2@cargotrace.test / Password123!
     */
    public function run(): void
    {
        // 1. Administrator
        User::firstOrCreate(
            ['email' => 'admin@cargotrace.test'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('Password123!'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 2. Logistics Operator
        User::firstOrCreate(
            ['email' => 'operator@cargotrace.test'],
            [
                'name' => 'Logistics Operations Lead',
                'password' => Hash::make('Password123!'),
                'role' => 'operator',
                'email_verified_at' => now(),
            ]
        );

        // 3. Customer 1 (Corporate)
        User::firstOrCreate(
            ['email' => 'customer1@cargotrace.test'],
            [
                'name' => 'PT Global Maritime Logistik',
                'password' => Hash::make('Password123!'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]
        );

        // 4. Customer 2 (Enterprise)
        User::firstOrCreate(
            ['email' => 'customer2@cargotrace.test'],
            [
                'name' => 'EuroTrade Logistics BV',
                'password' => Hash::make('Password123!'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]
        );
    }
}
