<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthJWTController extends Controller
{
    //register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);

        return Response()->json([
            'message' => 'User berhasil dibuat',
            'data' => $user,
        ]);
    }

    //login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        //JWT Token for login
        $token = JWTAuth::attempt([
            'email' => $request['email'],
            'password' => $request['password'],
        ]);

        //validasi token tidak ada
        if (!empty($token)) {
            return Response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Login Berhasil',
                'token' => $token
            ]);
        }
        return response()->json([
            'status' => Response::HTTP_UNAUTHORIZED,
            'message' => 'Login Gagal!',
        ]);
    }



    //profile
    public function profile()
    {
        $profile = auth()->user();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Profile User',
            'data' => $profile,
        ]);
    }

    //logout
    public function logout()
    {
        auth()->logout();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Logout Berhasil',
        ]);
    }

    //refresh token
    public function refresh()
    {
        $newToken = auth()->refresh();
        return response()->json([

            'status' => Response::HTTP_OK,
            'message' => 'New Token Generated',
            'token' => $newToken
        ]);
    }
}
