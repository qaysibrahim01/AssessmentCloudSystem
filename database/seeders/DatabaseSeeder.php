<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminEmail = 'admin@example.com';

        \App\Models\User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'admin',
                'password' => bcrypt('12345678'),
                'role' => 'admin',
                'approval_status' => 'approved',
                'is_approved' => true,
                'approved_at' => now(),
                'approval_email_sent_at' => null,
            ]
        );
    }
}
