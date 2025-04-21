<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Http;

class TransaksiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    protected $petaniList = [];
    protected $produkList = [];

    public function setExtra($petaniList, $produkList)
    {
        $this->petaniList = $petaniList;
        $this->produkList = $produkList;
        return $this;
    }

    public function toArray($request)
    {
        $namaPetani = $this->petaniList[$this->petani_id]['nama'] ?? 'Tidak Diketahui';
        $namaProduk = $this->produkList[$this->produk_id]['nama_produk'] ?? 'Tidak Diketahui';

        return [
            'id' => $this->id,
            // 'petani' => $namaPetani,
            // 'produk' => $namaProduk,
            'petani_id' => $this->petani_id,
            'produk_id' => $this->produk_id,
            'tanggal' => $this->tanggal,
            'jumlah' => $this->jumlah,
            'total_harga' => $this->total_harga,
        ];
    }
}
