<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\IncidentController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    
    // Middleware local pour vérifier que l'utilisateur n'a pas été désactivé entre-temps
    Route::middleware(function (Request $request, $next) {
        if (! $request->user() || ! $request->user()->is_active) {
            // Renvoyer 401 Unauthorized force l'application mobile à se déconnecter
            return response()->json(['message' => 'Votre compte a été désactivé.'], 401);
        }
        return $next($request);
    })->group(function () {

        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Endpoints pour la synchronisation
        Route::get('/sync/reference-data', [SyncController::class, 'getReferenceData']);
        Route::post('/incidents', [IncidentController::class, 'store']);
        
    });
});
