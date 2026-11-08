<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kategori_id' => ['required', 'exists:kategoris,id'],
            'judul'       => ['required', 'string', 'max:255'],
            'deskripsi'   => ['required', 'string', 'max:5000'],
            'lat'         => ['required', 'numeric', 'between:-90,90'],
            'lng'         => ['required', 'numeric', 'between:-180,180'],
            'foto_bukti'  => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'required'        => ':attribute wajib diisi.',
            'lat.required'    => 'Tentukan lokasi kejadian pada peta.',
            'lng.required'    => 'Tentukan lokasi kejadian pada peta.',
            'between'         => ':attribute di luar rentang yang valid.',
            'numeric'         => ':attribute harus berupa angka.',
            'exists'          => ':attribute yang dipilih tidak valid.',
            'max.string'      => ':attribute terlalu panjang (maksimal :max karakter).',
            'foto_bukti.image' => 'Foto bukti harus berupa gambar.',
            'foto_bukti.mimes' => 'Foto bukti harus berformat JPG atau PNG.',
            'foto_bukti.max'   => 'Ukuran foto bukti maksimal 2 MB.',
            'foto_bukti.uploaded' => 'Foto gagal diunggah. Pastikan ukurannya tidak lebih dari 2 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'kategori_id' => 'Kategori',
            'judul'       => 'Judul laporan',
            'deskripsi'   => 'Deskripsi',
            'foto_bukti'  => 'Foto bukti',
        ];
    }

    /** Koordinat 0,0 (tengah laut) berarti pengguna belum memilih lokasi. */
    protected function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('lat') && $this->filled('lng')
                && (float) $this->lat == 0.0 && (float) $this->lng == 0.0) {
                $validator->errors()->add('lat', 'Tentukan lokasi kejadian pada peta.');
            }
        });
    }
}
