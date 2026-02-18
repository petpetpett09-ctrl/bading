<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = ['HRM', 'SCM', 'FIN', 'MAN', 'INV', 'ORD', 'WAR', 'CRM', 'ECO'];
        $positions = ['manager', 'staff'];

        foreach ($roles as $role) {
            foreach ($positions as $position) {
                $email = strtolower($role.'.'.$position.'@montierp.com');

                User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => "$role ($position)",
                        'role' => $role,
                        'position' => $position,
                        'password' => Hash::make('password123'),
                        'email_verified_at' => now(),
                        'is_active' => true,   // new column
                    ]
                );
            }
        }

        // ❌ System Admin account removed – no ADMIN role or IT position exist
    }
}
