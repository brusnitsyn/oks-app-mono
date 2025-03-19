<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RoleScope;
use App\Models\Scope;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RoleScope::truncate();
        Scope::truncate();
        Role::truncate();

        $scopes = [
            [ 'name' => 'LOGGED_IN', 'description' => '' ], // 1

            // Пациент
            [ 'name' => 'CAN_CREATE_PATIENT', 'description' => 'Разрешает пользователю добавлять пациента' ], // 2
            [ 'name' => 'CAN_READ_PATIENT', 'description' => 'Разрешает пользователю просматривать пациента' ], // 3
            [ 'name' => 'CAN_UPDATE_PATIENT', 'description' => 'Разрешает пользователю редактировать пациента' ], // 4
            [ 'name' => 'CAN_DELETE_PATIENT', 'description' => 'Разрешает пользователю удалять пациента' ], // 5

            // Контрольные точки
            [ 'name' => 'CAN_READ_CONTROL_CALL', 'description' => 'Разрешает пользователю просматривать контрольные точки' ], // 6
            [ 'name' => 'CAN_UPDATE_CONTROL_CALL', 'description' => 'Разрешает пользователю редактировать контрольные точки' ], // 7
        ];

        $roles = [
            [ 'name' => 'Сотрудник МО', 'slug' => 'ROLE_DOCTOR' ], // 1
            [ 'name' => 'Оператор', 'slug' => 'ROLE_OPERATOR' ], // 2
            [ 'name' => 'Администратор', 'slug' => 'ROLE_ADMIN' ], // 3
            [ 'name' => 'Ответственный врач', 'slug' => 'ROLE_MAIN_DOCTOR' ], // 4
        ];

        $roleScopes = [
            [ 'role_id' => 1, 'scope_id' => 1 ],
            [ 'role_id' => 1, 'scope_id' => 2 ],
            [ 'role_id' => 1, 'scope_id' => 3 ],
            [ 'role_id' => 1, 'scope_id' => 4 ],

            [ 'role_id' => 2, 'scope_id' => 1 ],
            [ 'role_id' => 2, 'scope_id' => 3 ],
            [ 'role_id' => 2, 'scope_id' => 6 ],
            [ 'role_id' => 2, 'scope_id' => 7 ],

            [ 'role_id' => 3, 'scope_id' => 1 ],
            [ 'role_id' => 3, 'scope_id' => 2 ],
            [ 'role_id' => 3, 'scope_id' => 3 ],
            [ 'role_id' => 3, 'scope_id' => 4 ],
            [ 'role_id' => 3, 'scope_id' => 5 ],
            [ 'role_id' => 3, 'scope_id' => 6 ],
            [ 'role_id' => 3, 'scope_id' => 7 ],

            [ 'role_id' => 4, 'scope_id' => 1 ],
            [ 'role_id' => 4, 'scope_id' => 2 ],
            [ 'role_id' => 4, 'scope_id' => 3 ],
            [ 'role_id' => 4, 'scope_id' => 4 ],
            [ 'role_id' => 4, 'scope_id' => 5 ],
            [ 'role_id' => 4, 'scope_id' => 6 ],
            [ 'role_id' => 4, 'scope_id' => 7 ],
        ];

        Scope::insert($scopes);
        Role::insert($roles);
        RoleScope::insert($roleScopes);
    }
}
