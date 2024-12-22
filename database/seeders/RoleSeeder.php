<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'slug' => 'doctor',
            'name' => 'Врач'
        ]);
        Role::create([
            'slug' => 'operator',
            'name' => 'Оператор'
        ]);
        Role::create([
            'slug' => 'admin',
            'name' => 'Администратор'
        ]);
    }
}
