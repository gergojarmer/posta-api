<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @api {post} /api/login Bejelentkezés (token kérése)
     * @apiName Login
     * @apiGroup 1_Auth
     * @apiVersion 1.0.0
     *
     * @apiBody {String} email    E-mail cím.
     * @apiBody {String} password Jelszó.
     *
     * @apiSuccess {Object} user  A bejelentkezett felhasználó.
     * @apiSuccess {String} token Bearer token a védett végpontokhoz.
     *
     * @apiSuccessExample {json} Siker (200):
     *     {
     *       "user": { "id": 1, "name": "Admin", "email": "admin@example.com" },
     *       "token": "1|abcDEF123..."
     *     }
     *
     * @apiError (401) Unauthorized Hibás e-mail vagy jelszó.
     * @apiErrorExample {json} Hiba (401):
     *     { "message": "Hibás e-mail vagy jelszó." }
     */
    public function login(Request $request)
    {
        $adatok = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $adatok['email'])->first();

        if (!$user || !Hash::check($adatok['password'], $user->password)) {
            return response()->json(['message' => 'Hibás e-mail vagy jelszó.'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token]);
    }

    /**
     * @api {post} /api/logout Kijelentkezés (a token törlése)
     * @apiName Logout
     * @apiGroup 1_Auth
     * @apiVersion 1.0.0
     * @apiPermission Bearer token
     *
     * @apiHeader {String} Authorization Bearer &lt;token&gt;
     *
     * @apiSuccessExample {json} Siker (200):
     *     { "message": "Sikeres kijelentkezés." }
     *
     * @apiError (401) Unauthenticated Hiányzó vagy érvénytelen token.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sikeres kijelentkezés.']);
    }

    /**
     * @api {get} /api/user A bejelentkezett felhasználó adatai
     * @apiName User
     * @apiGroup 1_Auth
     * @apiVersion 1.0.0
     * @apiPermission Bearer token
     *
     * @apiHeader {String} Authorization Bearer &lt;token&gt;
     *
     * @apiSuccessExample {json} Siker (200):
     *     { "id": 1, "name": "Admin", "email": "admin@example.com" }
     */
    public function user(Request $request)
    {
        return $request->user();
    }
}
