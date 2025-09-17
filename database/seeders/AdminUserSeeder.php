<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем админа, если его еще нет
        if (!User::where('email', 'admin@mygarage.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@mygarage.com',
                'password' => Hash::make('admin123'),
                'currency' => 'UAH',
                'is_admin' => true,
            ]);
        }
    }
}