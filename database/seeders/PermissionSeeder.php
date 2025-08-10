<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;


class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
           'view',
           'create',
           'edit',
           'delete',
           'print',
           'export',
           'allow-all',
        ];

        foreach ($permissions as $value) {
             Permission::create(['name' => $value]);
        }
    }
}
