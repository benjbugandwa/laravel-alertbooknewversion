<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceProvider;

class ServiceProviderSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            'Prise en charge médicale',
            'Soutien psychologique et psychosocial',
            'Prise en charge juridique et judiciaire',
            'Soutien socio-économique',
            'Gestion de cas',
            'Autre',
        ];

        $providers = [
            ['USAID', 'Goma', 'Focal Point USAID', 'usaid.fp@example.org', '+243000000001'],
            ['UNFPA', 'Goma', 'Focal Point UNFPA', 'unfpa.fp@example.org', '+243000000002'],
            ['UNICEF', 'Goma', 'Focal Point UNICEF', 'unicef.fp@example.org', '+243000000003'],
            ['OMS', 'Goma', 'Focal Point OMS', 'oms.fp@example.org', '+243000000004'],
            ['UNHCR', 'Goma', 'Focal Point UNHCR', 'unhcr.fp@example.org', '+243000000005'],
            ['FONDATION PANZI', 'Bukavu', 'Focal Point Panzi', 'panzi.fp@example.org', '+243000000006'],
        ];

        foreach ($providers as [$name, $loc, $fp, $email, $phone]) {
            ServiceProvider::updateOrCreate(
                ['provider_name' => $name],
                [
                    'provider_location' => $loc,
                    'focalpoint_name' => $fp,
                    'focalpoint_email' => $email,
                    'focalpoint_number' => $phone,
                    'type_services_proposes' => $services, // JSON
                    'created_by' => 1,
                ]
            );
        }
    }
}
