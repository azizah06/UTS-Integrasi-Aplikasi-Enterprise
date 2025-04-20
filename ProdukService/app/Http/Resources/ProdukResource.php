<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Http;

class ProdukResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

     protected $urlMitra = 'http://127.0.0.1:8003/api/mitra';

    public function toArray(Request $request): array
    {
        // Ambil data mitra dari API mitra
        $mitra = Http::get($this->urlMitra . '/' . $this->mitra_id);
        $namaMitra = $mitra->successful() ? $mitra->json('data.nama') : null;

        return [
            'id' => $this->id,
            'nama_produk' => $this->nama_produk,
            'harga' => $this->harga,
            'kategori' => $this->kategori,
            'stok' => $this->stok,
            'mitra_id' => $this->mitra_id,
            'nama_mitra' => $namaMitra, // Tambahkan ini
        ];
    }
}
