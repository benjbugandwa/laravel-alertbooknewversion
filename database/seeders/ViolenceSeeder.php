<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Violence;

class ViolenceSeeder extends Seeder
{
    public function run(): void
    {
        $violences = [

            // ==============================
            // VIOLENCE SEXUELLE
            // ==============================
            [
                'id' => 1,
                'categorie_name' => 'Violence sexuelle',
                'violence_name' => 'Viol',
                'violence_description' => 'Pénétration non consentie par force, menace ou coercition.'
            ],
            [
                'id' => 2,
                'categorie_name' => 'Violence sexuelle',
                'violence_name' => 'Tentative de viol',
                'violence_description' => 'Tentative d’acte sexuel non consenti sans pénétration complète.'
            ],
            [
                'id' => 3,
                'categorie_name' => 'Violence sexuelle',
                'violence_name' => 'Agression sexuelle',
                'violence_description' => 'Contact sexuel non consenti.'
            ],
            [
                'id' => 4,
                'categorie_name' => 'Violence sexuelle',
                'violence_name' => 'Exploitation sexuelle',
                'violence_description' => 'Abus de position de pouvoir à des fins sexuelles.'
            ],
            [
                'id' => 5,
                'categorie_name' => 'Violence sexuelle',
                'violence_name' => 'Harcèlement sexuel',
                'violence_description' => 'Comportement sexuel non désiré créant un environnement hostile.'
            ],
            [
                'id' => 6,
                'categorie_name' => 'Violence sexuelle',
                'violence_name' => 'Mariage forcé',
                'violence_description' => 'Union imposée sans consentement libre.'
            ],

            // ==============================
            // VIOLENCE PHYSIQUE
            // ==============================
            [
                'id' => 100,
                'categorie_name' => 'Violence physique',
                'violence_name' => 'Agression physique',
                'violence_description' => 'Usage intentionnel de la force causant des blessures.'
            ],
            [
                'id' => 101,
                'categorie_name' => 'Violence physique',
                'violence_name' => 'Coups et blessures',
                'violence_description' => 'Violence corporelle entraînant des dommages physiques.'
            ],
            [
                'id' => 102,
                'categorie_name' => 'Violence physique',
                'violence_name' => 'Torture',
                'violence_description' => 'Souffrance intentionnelle infligée pour punir ou intimider.'
            ],

            // ==============================
            // VIOLENCE PSYCHOLOGIQUE
            // ==============================
            [
                'id' => 200,
                'categorie_name' => 'Violence psychologique',
                'violence_name' => 'Menaces',
                'violence_description' => 'Menaces de violence ou de préjudice.'
            ],
            [
                'id' => 201,
                'categorie_name' => 'Violence psychologique',
                'violence_name' => 'Humiliation',
                'violence_description' => 'Actes visant à rabaisser ou dénigrer la victime.'
            ],
            [
                'id' => 202,
                'categorie_name' => 'Violence psychologique',
                'violence_name' => 'Intimidation',
                'violence_description' => 'Pressions ou comportements visant à effrayer.'
            ],

            // ==============================
            // VIOLENCE SOCIO-ECONOMIQUE
            // ==============================
            [
                'id' => 300,
                'categorie_name' => 'Violence socio-économique',
                'violence_name' => 'Privation de ressources',
                'violence_description' => 'Refus d’accès à l’argent, nourriture ou biens essentiels.'
            ],
            [
                'id' => 301,
                'categorie_name' => 'Violence socio-économique',
                'violence_name' => 'Refus d’accès aux soins',
                'violence_description' => 'Empêcher l’accès aux services médicaux.'
            ],
            [
                'id' => 302,
                'categorie_name' => 'Violence socio-économique',
                'violence_name' => 'Expulsion du domicile',
                'violence_description' => 'Expulsion injustifiée du logement.'
            ],

            // ==============================
            // PRATIQUES TRADITIONNELLES NEFASTES
            // ==============================
            [
                'id' => 400,
                'categorie_name' => 'Pratiques traditionnelles néfastes',
                'violence_name' => 'Mutilations génitales féminines (MGF)',
                'violence_description' => 'Ablation partielle ou totale des organes génitaux féminins.'
            ],
            [
                'id' => 401,
                'categorie_name' => 'Pratiques traditionnelles néfastes',
                'violence_name' => 'Mariage précoce',
                'violence_description' => 'Mariage impliquant une personne mineure.'
            ],
        ];

        foreach ($violences as $v) {
            Violence::updateOrCreate(
                ['violence_name' => $v['violence_name']],
                $v
            );
        }
    }
}
