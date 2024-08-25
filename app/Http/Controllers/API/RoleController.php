<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Menampilkan Semua Data Role
        $allRoles = Role::all();

        return response()->json([
            "message"   => "Semua Data Role Berhasil Ditampilkan",

            "data"      => $allRoles
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        // Membuat Data Role
        Role::create($request->all());

        return response()->json([
            "message"   => "Data Role Berhasil Ditambahkan"
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $detailRole     = Role::with(['listUsers'])->find($id); 

        // Jika ID Role Tidak Ditemukan
        if (!$detailRole) {
            return response()->json([
                'message'   => 'ID Role Tidak Ditemukan'
            ], 404);
        }

        return response()->json([
            'message'   => "Detail Data Role Berhasil Ditampilkan",
            'data'      => $detailRole
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, string $id)
    {
        $role = Role::find($id);

        // Jika ID Role Tidak Ditemukan
        if (!$role) {
            return response()->json([
                'message'   => 'ID Role Tidak Ditemukan'
            ], 404);
        }

        // Update Data Role
        $role->update($request->all());

        return response()->json([
            'message'   => "Data Role Berhasil Diupdate"
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find($id);

        // Jika ID Role Tidak Ditemukan
        if (!$role) {
            return response()->json([
                'message'   => 'ID Role Tidak Ditemukan'
            ], 404);
        }

        // Hapus Data Role
        $role->delete();

        return response()->json([
            'message'   => "Data Role Berhasil Dihapus"
        ], 200);
    }
}