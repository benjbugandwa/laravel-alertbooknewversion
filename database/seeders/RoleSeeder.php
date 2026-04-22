<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['slug' => 'superadmin',  'name' => 'Super Administrateur', 'description' => 'Accès global (toutes provinces)'],
            ['slug' => 'admin',       'name' => 'Administrateur',       'description' => 'Gestion province (users/org selon règles)'],
            ['slug' => 'superviseur', 'name' => 'Superviseur',          'description' => 'Gestion incidents + validation (province)'],
            ['slug' => 'moniteur',    'name' => 'Moniteur',             'description' => 'Saisie incidents (province)'],
        ];

        foreach ($roles as $r) {
            Role::updateOrCreate(['slug' => $r['slug']], $r);
        }

        // Option pratique : assigner superadmin au 1er user (si existe)
        $firstUser = User::query()->orderBy('id')->first();
        if ($firstUser) {
            $superadmin = Role::where('slug', 'superadmin')->first();
            $firstUser->roles()->syncWithoutDetaching([$superadmin->id]);
        }
    }
}
