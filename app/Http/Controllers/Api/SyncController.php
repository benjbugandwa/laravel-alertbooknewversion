<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Province;
use App\Models\Territoire;
use App\Models\Chefferie;
use App\Models\Groupement;
use App\Models\Zonesante;
use App\Models\Airesante;
use App\Models\Evenement;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    /**
     * Renvoie toutes les données de référence nécessaires au mode hors-ligne du mobile.
     */
    public function getReferenceData()
    {
        return response()->json([
            'provinces' => Province::select('code_province', 'nom_province')->get(),
            'territoires' => Territoire::select('code_territoire', 'nom_territoire', 'code_province')->get(),
            'chefferies' => Chefferie::select('code_chefferie', 'nom_chefferie', 'code_territoire')->get(),
            'groupements' => Groupement::select('code_groupement', 'nom_groupement', 'code_chefferie')->get(),
            'zonesantes' => Zonesante::select('code_zonesante', 'nom_zonesante', 'code_territoire')->get(),
            'airesantes' => Airesante::select('code_airesante', 'nom_airesante', 'code_zonesante')->get(),
            'evenements' => Evenement::select('code_evenement', 'nom_evenement')->get(),
        ]);
    }
}
