<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BorrowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allBorrows = Borrow::with(['book', 'user'])->get();

        return response()->json([
            "message"   => "Berhasil Menampilkan Semua Data Peminjaman Buku",

            "data"      => $allBorrows
        ], 200);
    }


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
                'load_date'     => 'required|date_format:Y-m-d H:i:s|before:borrow_date',
                'borrow_date'   => 'required|date_format:Y-m-d H:i:s|after:load_date',
                'book_id'       => 'required|exists:books,id'
            ],

            //messages
            [
                'load_date'  => [
                    'required'      =>  'Kolom Waktu Peminjaman Harus Diisi',
                    'date_format'   =>  'Kolom Waktu Peminjaman Harus Diisi secara Detil, Mulai dari Tanggal hingga Detiknya',
                    'before'        =>  'Waktu Peminjaman Harus Sebelum Waktu Pengembalian'
                ],

                'borrow_date'  => [
                    'required'      =>  'Kolom Waktu Pengembalian Harus Diisi',
                    'date_format'   =>  'Kolom Waktu Pengembalian Harus Diisi secara Detil, Mulai dari Tanggal hingga Detiknya',
                    'after'         =>  'Waktu Pengembalian Harus Sesudah Waktu Peminjaman'
                ],

                'book_id'  => [
                    'required'  =>  'Kolom Judul Buku Harus Diisi',
                    'exists'    =>  'ID Buku yang Anda Isi Tidak Ditemukan'
                ]
            ]
        );

        // Jika Tidak Lulus Validasi
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $currentUser = auth()->user();

        $bookRequest = Book::find($request->book_id);

        $borrowFromCurrentUser = Borrow::updateOrCreate(
            // kondisi : jika di table borrows sudah punya book_id dan user_id ini
            [
                'book_id'  =>   $request->book_id,
                'user_id'  =>   $currentUser->id
            ],

            // maka update semua kolom di baris pada table borrows tersebut
            [
                'load_date'     => $request->load_date,
                'borrow_date'   => $request->borrow_date
            ]

            // jika suatu user belum Borrow, maka akan dibuatkan Borrow dengan data-data di atas
        );

        // Jika Stok Buku yang Di-Request Stoknya Masih Ada
        if ($bookRequest->stok > 0) {

            // Jika Data Peminjaman Ini Adalah Hasil Create/Store, ...
            if ($borrowFromCurrentUser->wasRecentlyCreated) {

                // ... maka stok Buku Berkurang Setelah Dilakukan Peminjaman 
                $bookRequest->decreaseStock();

                $detailBorrowFromCurrentUser = Borrow::with(['book', 'user'])->find($borrowFromCurrentUser->id);

                return response()->json([
                    "message"   => "Data Peminjaman Buku Berhasil Ditambahkan",
                    "data"      => $detailBorrowFromCurrentUser
                ], 201);

            } else {
                $detailBorrowFromCurrentUser = Borrow::with(['book', 'user'])->find($borrowFromCurrentUser->id);

                return response()->json([
                    "message"   => "Data Peminjaman Buku Berhasil Diupdate",
                    "data"      => $detailBorrowFromCurrentUser
                ], 201);
            }

        } else {

            // maka yang diterima hanyalah method Update
            if (!$borrowFromCurrentUser->wasRecentlyCreated) {
                $detailBorrowFromCurrentUser = Borrow::with(['book', 'user'])->find($borrowFromCurrentUser->id);

                return response()->json([
                    "message"   => "Data Peminjaman Buku Berhasil Diupdate",
                    "data"      => $detailBorrowFromCurrentUser
                ], 201);
            } else {
                return response()->json([
                    "message"   => "Maaf, Saat Ini, Stok Buku yang Dimaksud Masih Kosong"
                ]);
            }
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $borrow = Borrow::find($id);

        // Jika ID Peminjaman Buku Tidak Ditemukan
        if (!$borrow) {
            return response()->json([
                'message'   => 'ID Peminjaman Buku Tidak Ditemukan'
            ], 404);
        }

        $bookBorrowed = Book::find($borrow->book_id);

        // Tambah Stok Buku
        $bookBorrowed->increaseStock();

        // Hapus Data Peminjaman Buku
        $borrow->delete();

        return response()->json([
            'message'   => "Buku Sudah Dikembalikan, dan Data Peminjaman Buku Berhasil Dihapus"
        ], 200);
    }
}