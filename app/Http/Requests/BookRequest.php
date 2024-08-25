<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'         => 'required|max:255',
            'author'        => 'required|max:100',
            'release_year'  => 'required|integer|max_digits:8',
            'summary'       => 'required|max:65535',
            'stok'          => 'required|integer|max_digits:8',
            'category_id'   => 'required|exists:categories,id'
        ];
    }

    /**
    * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title'         => [
                'required'  =>  'Kolom Judul Harus Diisi',
                'max'       =>  'Kolom Judul Tidak Boleh Diisi Lebih dari 255 Karakter'
            ],

            'author'         => [
                'required'  =>  'Kolom Penulis Harus Diisi',
                'max'       =>  'Kolom Penulis Tidak Boleh Diisi Lebih dari 100 Karakter'
            ],

            'release_year'          => [
                'required'  =>  'Kolom Tahun Rilis Harus Diisi',
                'integer'   =>  'Kolom Tahun Rilis Harus Diisi dengan Bilangan Bulat',
                'max'       =>  'Kolom Tahun Rilis Tidak Boleh Diisi Lebih dari 8 Digit Angka'
            ],

            'summary'       => [
                'required'  =>  'Kolom Ringkasan Harus Diisi',
                'max'       =>  'Kolom Ringkasan Tidak Boleh Diisi Lebih dari 65.535 Karakter'
            ],

            'stok'          => [
                'required'  =>  'Kolom Stok Harus Diisi',
                'integer'   =>  'Kolom Stok Harus Diisi dengan Bilangan Bulat',
                'max'       =>  'Kolom Stok Tidak Boleh Diisi Lebih dari 8 Digit Angka'
            ],

            'category_id'   => [
                'required'  =>  'Kolom Kategori Harus Diisi',
                'exists'    =>  'ID Kategori yang Anda Isi Tidak Ditemukan'
            ]
        ];
    }
}