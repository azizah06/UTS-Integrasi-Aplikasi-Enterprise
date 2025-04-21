<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MitraResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_mitra' => $this->nama_mitra,
            'alamat_mitra' => $this->alamat_mitra,
            'no_telp_mitra' => $this->no_telp_mitra,
        ];
    }
}
