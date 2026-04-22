<?php

namespace Database\Seeders;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IncidentSeeder extends Seeder
{
    public function run(): void
    {
        $province = 'CD61';

        $territoires = [
            'CD6101',
            'CD6102',
            'CD6103',
        ];

        $zones = [
            'CD6101ZS01',
            'CD6103ZS01',
            'CD6103ZS04',
        ];

        $statuts = [
            'Nouveau',
            'En cours',
            'Référé',
            'Clôturé',
        ];

        $severites = [
            'Faible',
            'Moyenne',
            'Élevée',
            'Critique',
        ];

        $confidentialities = [
            'Low',
            'Medium',
            'High',
        ];

        $users = User::pluck('id')->toArray();
        if (empty($users)) {
            $this->command->warn('Aucun utilisateur trouvé. Seeder annulé.');
            return;
        }

        for ($i = 1; $i <= 120; $i++) {

            Incident::create([
                'id' => Str::uuid(),
                'code_incident' => 'INC-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),

                'date_incident' => now()->subDays(rand(0, 120)),
                'created_by' => $users[array_rand($users)],

                'severite' => $severites[array_rand($severites)],
                'statut_incident' => $statuts[array_rand($statuts)],
                'auteur_presume' => 'Auteur inconnu',

                'code_province' => $province,
                'code_territoire' => $territoires[array_rand($territoires)],
                'code_zonesante' => $zones[array_rand($zones)],

                'localite' => 'Localité ' . rand(1, 20),
                'source_info' => 'Signalement communautaire',
                'description_faits' => 'Description aléatoire de test pour dashboard.',

                'created_at' => now(),
                'assigned_to' => null,
                'assigned_by' => null,
                'assigned_at' => null,
                'last_status_changed_at' => now(),

                'confidentiality_level' => $confidentialities[array_rand($confidentialities)],
                'photo_url' => null,
            ]);
        }

        $this->command->info('Incidents de test générés avec succès.');
    }
}
