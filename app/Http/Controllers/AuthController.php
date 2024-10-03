<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    //variable untuk custom header
    public const Header = [
        'X-PARTNER-ID' => 'RINTIS',
        'X-TIMESTAMP' => '2024-10-02',
        'X-SIGNATURE' => 'abc123',

    ];

    //register
    public function register(Request $request)
    {
        //validasi request register
        $input = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        //buat user
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => bcrypt($input['password']),
        ]);

        //buat token
        $token = $user->createToken('secretKey')->plainTextToken;
        $data = [
            'status' => Response::HTTP_CREATED,
            'message' => 'User berhasil dibuat',
            'data' => $user,
            'token' => $token,
            'type' => 'bearer'
        ];
        return Response()->json($data, Response::HTTP_CREATED);
    }


    //login
    public function login(Request $request)
    {
        //validasi request login
        $input = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        //authentication user cek
        $user = User::where('email', $input['email'])->first();

        //user tidak ditemukan & password salah
        if (!$user || !Hash::check($input['password'], $user->password)) {
            return Response()->json(
                [
                    'status' => Response::HTTP_UNAUTHORIZED,
                    'message' => 'Invalid credentials',
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $token = $user->createToken('secretKey')->plainTextToken;
        $data = [
            'status' => Response::HTTP_OK,
            'message' => 'User berhasil login',
            'data' => $user,
            'token' => $token,
            'type' => 'bearer'
        ];
        return Response()->json($data, Response::HTTP_OK);
    }



    //user
    public function user()
    {
        $data = [
            'status' => Response::HTTP_OK,
            'message' => 'Detail User',
            'data' => auth()->user(),
        ];
        return Response()->json($data, Response::HTTP_OK);
    }

    //logout
    public function logout()
    {
        auth()->user()->tokens->each(function ($token) {
            $token->delete();
        });
        $data = [
            'status' => Response::HTTP_OK,
            'message' => 'Berhasil logout'
        ];
        return response()->json($data, Response::HTTP_OK);
    }
}
