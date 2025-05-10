<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'role' => 'Admin',
            'password' => Hash::make('12345678'),
            'foto' => null,
        ]);

        User::create([
            'name' => 'Kepala Bagian',
            'email' => 'kepala@example.com',
            'email_verified_at' => now(),
            'role' => 'Kepala',
            'password' => Hash::make('12345678'),
            'foto' => null,
        ]);

        User::create([
            'name' => 'User Biasa',
            'email' => 'user@example.com',
            'email_verified_at' => now(),
            'role' => 'User',
            'password' => Hash::make('12345678'),
            'foto' => null,
        ]);
    }
}
