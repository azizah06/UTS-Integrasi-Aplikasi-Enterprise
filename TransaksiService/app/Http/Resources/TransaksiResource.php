<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Http;

class TransaksiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    protected $urlPetani = 'http://127.0.0.1:8000/api/petani';
    protected $urlProduk = 'http://127.0.0.1:8001/api/produk';
    public function toArray($request)
    {
        // Ambil nama petani dari service petani
        $petani = Http::get($this->urlPetani . '/' . $this->petani_id);
        $namaPetani = $petani->successful() ? $petani->json('data.nama') : null;

        // Ambil nama produk dari service produk
        $produk = Http::get($this->urlProduk . '/' . $this->produk_id);
        $namaProduk = $produk->successful() ? $produk->json('data.nama_produk') : null;

        return [
            'id' => $this->id,
            'petani_id' => $this->petani_id,
            'nama_petani' => $namaPetani,
            'produk_id' => $this->produk_id,
            'nama_produk' => $namaProduk,
            'tanggal' => $this->tanggal,
            'jumlah' => $this->jumlah,
            'total_harga' => $this->total_harga,
        ];
    }
}
