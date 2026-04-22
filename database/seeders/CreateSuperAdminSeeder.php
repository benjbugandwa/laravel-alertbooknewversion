<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@gbv.test'],
            [
                'name' => 'Super Admin test',
                'password' => Hash::make('Password123!'),
                'is_active' => true,
                // 'code_province' => 'PROV01',
                'code_province' => 'CD61',

                // ou une valeur existante dans provinces
            ]
        );

        $super = Role::where('slug', 'superadmin')->firstOrFail();

        $user->roles()->syncWithoutDetaching([$super->id]);
        $user->user_role = 'superadmin'; // optionnel
        $user->save();
    }
}
