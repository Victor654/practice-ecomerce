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
        $this->middleware('auth:api', ['except' => ['login', 'tokenFailures']]);
    }

    public function tokenFailures()
    {
        $meta =  [
            'success' => false,
            'errors' => [
                'Token expired'
            ]
        ];

        return response()->json(['meta' => $meta], 401);
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
                'success' => false,
                'errors' => [
                    "Password incorrect for: ".  request('username')
                ]
            ];

            return response()->json(['meta' => $meta], 401);
        }

        if ( auth()->user()->is_active) {
            auth()->user()->last_login = date('Y-m-d H:i:s');
            auth()->user()->save();
    
            return $this->respondWithToken($token);
        } else {
            $meta =  [
                'success' => false,
                'errors' => [
                    "User no active"
                ]
            ];

            return response()->json(['meta' => $meta], 401);
        }
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

        
        $meta =  [
            'success' => true,
            'errors' => []
        ];

        $data =  [
            'message' => 'Successfully logged out'
        ];

        return response()->json([
            'meta' => $meta,
            'data' => $data
        ], 202);
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
            'minutes_to_expire' => auth()->factory()->getTTL()
        ];

        return response()->json([
            'meta' => $meta,
            'data' => $data
        ]);
    }
}
