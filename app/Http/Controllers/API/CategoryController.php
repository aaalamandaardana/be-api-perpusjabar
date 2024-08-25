<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth:api', 'isOwner'])->only(['store', 'update', 'destroy']);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Menampilkan Semua Data Kategori
        $allCategories = Category::all();

        return response()->json([
            "message"   => "Semua Data Kategori Berhasil Ditampilkan",

            "data"      => $allCategories
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        // Membuat Data Kategori
        Category::create($request->all());

        return response()->json([
            "message"   => "Data Kategori Berhasil Ditambahkan"
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $detailCategory     = Category::with(['listBooks'])->find($id); 

        // Jika ID Kategori Tidak Ditemukan
        if (!$detailCategory) {
            return response()->json([
                'message'   => 'ID Kategori Tidak Ditemukan'
            ], 404);
        }

        return response()->json([
            'message'   => "Detail Data Kategori Berhasil Ditampilkan",
            'data'      => $detailCategory
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $id)
    {
        $category = Category::find($id);

        // Jika ID Kategori Tidak Ditemukan
        if (!$category) {
            return response()->json([
                'message'   => 'ID Kategori Tidak Ditemukan'
            ], 404);
        }

        // Update Data Kategori
        $category->update($request->all());

        return response()->json([
            'message'   => "Data Kategori Berhasil Diupdate"
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);

        // Jika ID Kategori Tidak Ditemukan
        if (!$category) {
            return response()->json([
                'message'   => 'ID Kategori Tidak Ditemukan'
            ], 404);
        }

        // Hapus Data Kategori
        $category->delete();

        return response()->json([
            'message'   => "Data Kategori Berhasil Dihapus"
        ], 200);
    }
}
