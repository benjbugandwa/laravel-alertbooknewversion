<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\IncidentController;

use App\Http\Middleware\CheckUserIsActive;

Route::get('/test-connection', function () {
    return response()->json(['status' => 'ok', 'message' => 'API AlertBook accessible !']);
});

Route::get('/fix-sequence', function () {
    try {
        DB::statement("
            SELECT setval('incident_code_seq', 
                (SELECT COALESCE(MAX(CAST(SUBSTRING(code_incident FROM 5) AS INTEGER)), 0) 
                 FROM incidents 
                 WHERE code_incident ~ '^ALT-[0-9]+$')
            )
        ");
        return response()->json(['status' => 'success', 'message' => 'Séquence synchronisée avec succès !']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', CheckUserIsActive::class])->group(function () {
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Endpoints pour la synchronisation
    Route::get('/sync/reference-data', [SyncController::class, 'getReferenceData']);
    Route::post('/incidents', [IncidentController::class, 'store']);
    
});
