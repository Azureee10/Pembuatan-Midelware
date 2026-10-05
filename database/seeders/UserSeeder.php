<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@minimarket.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir@minimarket.test',
            'password' => Hash::make('password'),
            'role' => 'kasir',
        ]);
    }
}
