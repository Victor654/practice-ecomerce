<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
            $credentials = request(['username', 'password']);
            if (! $token = auth()->attempt($credentials)) {
                $meta =  [
                    'success' => true,
                    'errors' => [
                        "Password incorrect for: ".  request('username')
                    ]
                ];

                return response()->json(['meta' => $meta], 401);
            }
    
            auth()->user()->last_login = date('Y-m-d H:i:s');
            auth()->user()->save();
    
            return $this->respondWithToken($token);

    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {

        $meta =  [
            'success' => true,
            'errors' => []
        ];

        $data =  [
            'token' => $token,
            'minutes_to_expire' => auth()->factory()->getTTL() * 24
        ];

        return response()->json([
            'meta' => $meta,
            'data' => $data
        ]);
    }
}
