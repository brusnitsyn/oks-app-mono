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
            'name' => 'Администратор',
            'login' => 'admin',
            'password' => Hash::make('1234567'),
        ]);
        User::create([
            'name' => 'Оператор',
            'login' => 'operator',
            'password' => Hash::make('1234567'),
        ]);
        User::create([
            'name' => 'Врач',
            'login' => 'doctor',
            'password' => Hash::make('1234567'),
        ]);
    }
}
