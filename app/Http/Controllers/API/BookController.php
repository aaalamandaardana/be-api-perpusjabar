<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;
use App\Models\Book;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
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
        // Menampilkan Semua Data Buku
        $allBooks = Book::with('category')
            ->orderBy('release_year', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            "message"   => "Semua Data Buku Berhasil Ditampilkan",

            "data"      => $allBooks
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookRequest $request)
    {
        // Validator Khusus File Sampul (image)
        $validatorImage = Validator::make($request->all(), 
            // rules
            [
                'image'         => 'required|mimes:jpg,jpeg,bmp,png',
            ],

            //messages
            [
                'image'         => [
                    'required'  =>  'Kolom Sampul Harus Diisi',
                    'mimes'     =>  'Kolom Sampul Hanya Dapat Diisi dengan Gambar yang Berbentuk jpg, jpeg, bmp, atau png'
                ],
            ]
        );

        // Jika Tidak Lulus Validasi Khusus File Sampul (image)
        if ($validatorImage->fails()) {
            return response()->json($validatorImage->errors(), 422);
        }

        // Mendefinisikan Kumpulan $request yang Sudah Lolos Semua Validasi
        $dataValidated = $request->validated();

        // Jika Ada File Sampul (image) yang Diunggah 
        if ($request->hasFile('image')) {

            $cloudinaryImage = $request->file('image')->storeOnCloudinary('books');
            $url = $cloudinaryImage->getSecurePath();
            $public_id = $cloudinaryImage->getPublicId();
            
            $dataValidated['image_url']           = $url;
            $dataValidated['image_public_id']     = $public_id;
        }

        // Membuat Data Buku
        Book::create($dataValidated);

        return response()->json([
            "message"   => "Data Buku Berhasil Ditambahkan"
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $detailBook     = Book::with(['category', 'listBorrows'])->find($id); 

        // Jika ID Buku Tidak Ditemukan
        if (!$detailBook) {
            return response()->json([
                'message'   => 'ID Buku Tidak Ditemukan'
            ], 404);
        }

        return response()->json([
            'message'   => "Detail Data Buku Berhasil Ditampilkan",
            'data'      => $detailBook
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookRequest $request, string $id)
    {
        $book = Book::find($id);

        // Jika ID Buku Tidak Ditemukan
        if (!$book) {
            return response()->json([
                'message'   => 'ID Buku Tidak Ditemukan'
            ], 404);
        }

        // Mendefinisikan Kumpulan $request yang Sudah Lolos Semua Validasi
        $dataValidated = $request->validated();

        // Jika Ada File Sampul (image) yang Diunggah 
        if ($request->hasFile('image')) {

            // Validator Khusus File Sampul (image)
            $validatorImage = Validator::make($request->all(), 
                // rules
                [
                    'image'         => 'mimes:jpg,jpeg,bmp,png',
                ],

                //messages
                [
                    'image'         => [
                        'mimes'     =>  'Kolom Sampul Hanya Dapat Diisi dengan Gambar yang Berbentuk jpg, jpeg, bmp, atau png'
                    ],
                ]
            );

            // Jika Tidak Lulus Validasi Khusus File Sampul (image)
            if ($validatorImage->fails()) {
                return response()->json($validatorImage->errors(), 422);
            }

            // jika sebelumnya kolom sampul memiliki file yang telah diunggah alias tidak null
            if ($book->image_public_id) {

                Cloudinary::destroy($book->image_public_id);

            }

            $cloudinaryImage = $request->file('image')->storeOnCloudinary('books');
            $url = $cloudinaryImage->getSecurePath();
            $public_id = $cloudinaryImage->getPublicId();
            
            $dataValidated['image_url']           = $url;
            $dataValidated['image_public_id']     = $public_id;

        } else {
            $dataValidated['image_url'] = $book->image_url;
            $dataValidated['image_public_id']     = $book->image_public_id;
        }

        // Update Data Buku
        $book->update($dataValidated);

        return response()->json([
            'message'   => "Data Buku Berhasil Diupdate"
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = Book::find($id);

        // Jika ID Buku Tidak Ditemukan
        if (!$book) {
            return response()->json([
                'message'   => 'ID Buku Tidak Ditemukan'
            ], 404);
        }

        // Jika Sebelumnya Kolom image Memiliki File yang Telah Diunggah alias Tidak Null
        if ($book->image_url) {

            Cloudinary::destroy($book->image_public_id);

        }

        // Hapus Data Buku
        $book->delete();

        return response()->json([
            'message'   => "Data Buku Berhasil Dihapus",
        ], 200);
    }
}
