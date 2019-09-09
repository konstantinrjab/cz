<?php

namespace App\Http\Controllers;

use App\Entity\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\JWTGuard;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = new User;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json(['status' => 'success'], JsonResponse::HTTP_OK);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        if ($token = $this->guard()->attempt($credentials)) {

            return response()->json(['status' => 'success'], JsonResponse::HTTP_OK)
                ->header('Authorization', $token);
        }

        return response()->json(['error' => 'login_error'], JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function logout(): JsonResponse
    {
        $this->guard()->logout();

        return response()->json([
            'status' => 'success',
            'message'    => 'Logged out Successfully.'
        ], JsonResponse::HTTP_OK);
    }

    public function user(): JsonResponse
    {
        $user = User::query()->find(Auth::user()->getAuthIdentifier());

        return response()->json([
            'status' => 'success',
            'data'   => $user
        ]);
    }

    public function refresh(): JsonResponse
    {
        if ($token = $this->guard()->refresh()) {

            return response()
                ->json(['status' => 'success'], JsonResponse::HTTP_OK)
                ->header('Authorization', $token);
        }

        return response()->json(['error' => 'refresh token error'], JsonResponse::HTTP_UNAUTHORIZED);
    }

    private function guard(): JWTGuard
    {
        return Auth::guard();
    }
}
