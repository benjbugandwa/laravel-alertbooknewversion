<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MapIncidentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $territoires = DB::table('territoires')->get();
        if ($territoires->isEmpty()) {
            $this->command->error('Aucun territoire trouvé dans la base de données. Veuillez d\'abord peupler la table territoires.');
            return;
        }

        $statuts = ['Nouveau', 'En cours', 'Clôturé', 'Rejeté'];
        $evenements = ['EVENT1', 'EVENT16', 'EVENT5', 'EVENT4'];

        $count = 0;

        for ($i = 0; $i < 50; $i++) {
            $territoire = $territoires->random();
            
            DB::table('incidents')->insert([
                'id' => Str::uuid()->toString(),
                'code_incident' => 'ALT-MAP-' . strtoupper(Str::random(6)) . '-' . $i,
                'date_incident' => Carbon::now()->subDays(rand(1, 30)),
                'code_province' => $territoire->code_province,
                'code_territoire' => $territoire->code_territoire,
                'statut_incident' => $statuts[array_rand($statuts)],
                'code_evenement' => $evenements[array_rand($evenements)],
                'severite' => rand(1, 5) > 3 ? 'Haute' : 'Moyenne',
                'description_faits' => 'Incident généré automatiquement pour tester la carte.',
                'created_at' => now(),
            ]);

            $count++;
        }

        $this->command->info("$count incidents ont été générés avec succès pour tester la carte.");
    }
}
