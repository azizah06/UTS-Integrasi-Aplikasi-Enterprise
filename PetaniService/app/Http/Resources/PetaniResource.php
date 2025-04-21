<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Http;

class PetaniResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        // $transaksiCount = 0;

        try {
            $response = Http::get(config('services.transaksi_service.base_url') . '/petani/' . $this->id);
            if ($response->successful()) {
                $data = $response->json();
                // $transaksiCount = count($data['data'] ?? []);
            }
        } catch (\Exception $e) {
            // Optional: bisa log error kalau mau
        }
    
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'alamat' => $this->alamat,
            'nomor_telepon' => $this->nomor_telepon,
            // 'total_transaksi' => $transaksiCount
        ];
    }
    
}
