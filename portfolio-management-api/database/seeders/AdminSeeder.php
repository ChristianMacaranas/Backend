<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $username = env('ADMIN_USERNAME', 'admin');
        $password = env('ADMIN_PASSWORD', 'password123');

        Admin::updateOrCreate(
            ['username' => $username],
            [
                'password' => Hash::make($password),
                'name' => 'Portfolio Owner',
                'email' => env('ADMIN_EMAIL'),
            ]
        );
    }
}
