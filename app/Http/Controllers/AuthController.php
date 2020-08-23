<?php

namespace App\Http\Controllers;

use App\Events\LoggedIn;
use App\Events\LoggedOut;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ThrottlesLogins;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    protected $maxAttempts = 5; // Amount of bad attempts user can make
    protected $decayMinutes = 5; // Time for which user is going to be blocked in seconds

    protected function username() {
        return 'error';
    }

    public function login(Request $request)
    {
        $credentials = request(['name', 'password']);
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        } else {
            if (!$token = auth()->attempt($credentials)) {
                $this->incrementLoginAttempts($request);
                return response()->json([
                    'code' => CODE_ERROR,
                    'message' => 'Thông tin đăng nhập không đúng!'
                ], 401);
            }
            if(auth()->user()->status == 0){
                $this->incrementLoginAttempts($request);
                return response()->json([
                    'code' => CODE_ERROR,
                    'message' => 'Tài khoản đã bị khóa'
                ], 401);
            }
            event(new LoggedIn(auth()->user()));
            return $this->respondWithToken($token);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = auth()->user();
        return response()->json([
            'user' => $user,
            'role' => $user->roles()->select('roles.id', 'name')->first()
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        event(new LoggedOut(auth()->user()));
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

        return response()->json([
            'code' => CODE_SUCCESS,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
