<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Option 1 : Update the specified resource in storage.
     * Option 2 : Store a newly created resource in storage.
     */
    public function updateOrStore(Request $request)
    {
        // Validator Data
        $validator = Validator::make($request->all(), 
            // rules
            [
                'age'       => 'required|integer|max_digits:11',
                'bio'       => 'required|string|max:65535'
            ],

            //messages
            [
                'age'  => [
                    'required'      =>  'Kolom Usia Harus Diisi',
                    'integer'       =>  'Kolom Usia Harus Diisi dengan Bilangan Bulat',
                    'max_digits'    =>  'Kolom Usia Tidak Boleh Diisi Lebih dari 11 Digit Angka'
                ],

                'bio'  => [
                    'required'  =>  'Kolom Bio Harus Diisi',
                    'string'    =>  'Isi Kolom Bio Harus Memiliki Tipe Data Berupa string',
                    'max'       =>  'Kolom Bio Tidak Boleh Diisi Lebih dari 65.535 Karakter'
                ]
            ]
        );

        // Jika Tidak Lulus Validasi
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $currentUser = auth()->user();

        $profileCurrentUser = Profile::updateOrCreate(
            // kondisi : jika di table profile sudah punya user_id ini
            ['user_id'  =>  $currentUser->id],

            // maka update semua kolom di baris pada table profile tersebut
            [
                'age'       => $request->age,
                'bio'       => $request->bio
            ]

            // jika suatu user belum punya Profile, maka akan dibuatkan Profile dengan data-data di atas
        );

        // Jika Data Profil Ini Adalah Hasil Create/Store, ...
        if ($profileCurrentUser->wasRecentlyCreated) {

            return response()->json([
                "message"   => "Profil Anda Berhasil Dibuat",
                "data"      => $profileCurrentUser
            ], 201);

        } else {
            return response()->json([
                "message"   => "Profil Anda Berhasil Diupdate",
                "data"      => $profileCurrentUser
            ], 201);
        }
    }
}