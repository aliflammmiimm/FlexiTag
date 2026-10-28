<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLostModeRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna berhak melakukan request ini.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Aturan validasi server untuk formulir Mode Hilang.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_barang' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:5', 'max:250'],
            'relay_phone' => ['required', 'string', 'min:8', 'max:25'],
            'reward' => ['nullable', 'string', 'max:100'],
            'instruksi_pengembalian' => ['nullable', 'string', 'max:250'],
            'form_enabled' => ['nullable', 'boolean'],
            'share_location' => ['nullable', 'boolean'],
            'activate_mode' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Pesan error validasi dalam bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_barang.max' => 'Nama barang maksimal 150 karakter.',
            'message.required' => 'Pesan penemuan barang darurat wajib diisi.',
            'message.min' => 'Pesan penemuan minimal 5 karakter.',
            'message.max' => 'Pesan penemuan maksimal 250 karakter.',
            'relay_phone.required' => 'Nomor HP penerima relay wajib diisi.',
            'relay_phone.min' => 'Nomor HP penerima relay minimal 8 digit.',
            'relay_phone.max' => 'Nomor HP penerima relay maksimal 25 karakter.',
            'reward.max' => 'Informasi imbalan maksimal 100 karakter.',
            'instruksi_pengembalian.max' => 'Arahan pengembalian maksimal 250 karakter.',
        ];
    }
}
