<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Create User (And Token Automatically).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validator Data
        $validator = Validator::make($request->all(), 
            // rules
            [
                'name'      => 'required|string|max:255',
                'email'     => 'required|string|email|max:255|unique:users,email',
                'password'  => 'required|string|min:8|confirmed'
            ],

            //messages
            [
                'name'  => [
                    'required'  =>  'Kolom Nama Harus Diisi',
                    'string'    =>  'Isi Kolom Nama Harus Memiliki Tipe Data Berupa string',
                    'max'       =>  'Kolom Nama Tidak Boleh Diisi Lebih dari 255 Karakter'
                ],

                'email'  => [
                    'required'  =>  'Kolom Email Harus Diisi',
                    'string'    =>  'Isi Kolom Email Harus Memiliki Tipe Data Berupa string',
                    'email'     =>  'Isi Kolom Email Harus Sesuai dengan Format Alamat Email',
                    'max'       =>  'Kolom Email Tidak Boleh Diisi Lebih dari 255 Karakter',
                    'unique'    =>  'Isi Kolom Email pada Suatu User Harus Berbeda dengan User Lainnya'
                ],

                'password'  => [
                    'required'  =>  'Kolom Password Harus Diisi',
                    'string'    =>  'Isi Kolom Password Harus Memiliki Tipe Data Berupa string',
                    'min'       =>  'Kolom Password Harus Diisi Minimal dengan 8 Karakter',
                    'confirmed' =>  'Isi Kolom Konfirmasi Password Harus Sesuai dengan Isi Kolom Password'
                ],
            ]
        );

        // Jika Tidak Lulus Validasi
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Ambil Data Role User
        $roleUser = Role::where('name', 'user')->first();

        // Penambahan Data User dengan Role user
        $userCreated = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role_id'   => $roleUser->id
        ]);

        // Generate Token untuk User yang Baru Dibuat
        $token = JWTAuth::fromUser($userCreated);

        // Detail Data User yang Baru Dibuat
        $detailUserCreated  = User::with(['role', 'profile', 'listBorrows'])->find($userCreated->id);

        return response()->json([
            'message'   => 'Registrasi Akun Berhasil',
            'token'     => $token,
            'user'      => $detailUserCreated
        ], 201);
    }


    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Kalo Misalkan Inputan Email dan Password Tidak Sesuai dengan Satupun Email dan Password suatu User
        if (!auth()->attempt($credentials)) {
            return response()->json([
                'error' => 'User Invalid'
            ], 401);
        }

        // Detai Data User yang Login        
        $userLogin   = User::with(['role','profile', 'listBorrows'])->where('email', $request['email'])->first();

        // Generate Token
        $token      = JWTAuth::fromUser($userLogin);

        return response()->json([
            'message'   => 'Login Berhasil',
            'token'     => $token,
            'user'      => $userLogin
        ], 200);
    }


    /**
     * Get the authenticated Current User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMe()
    {
        $idCurrentUser    = auth()->id();

        $currentUser = User::with(['role','profile', 'listBorrows'])->find($idCurrentUser);

        return response()->json([
            'message'   => 'Berhasil Mendapatkan Data Current User',
            'user'      => $currentUser
        ], 200);
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'message' => 'Logout Berhasil'
        ], 200);
    }
}