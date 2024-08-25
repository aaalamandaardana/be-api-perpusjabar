<?php

namespace Database\Seeders;

use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $listRoles = [
            ['name' =>  'owner'],
            ['name' =>  'user']
        ];
        
        foreach ($listRoles as $key => $value) {
            Role::create($value);
        }
    }
}