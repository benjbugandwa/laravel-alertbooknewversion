<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        if (! $user->is_active) {
            return response()->json([
                'message' => 'Votre compte n\'est pas actif. Veuillez contacter l\'administrateur.'
            ], 403);
        }

        // Generate Sanctum token
        $token = $user->createToken('mobile-app-token')->plainTextToken;

        $user->load('organisation');

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name ?? '',
                'email' => $user->email,
                'organization' => $user->organisation ? $user->organisation->nom_org : 'Sans organisation',
                'user_role' => $user->user_role,
                'code_province' => $user->code_province,
            ],
        ]);
    }
}
