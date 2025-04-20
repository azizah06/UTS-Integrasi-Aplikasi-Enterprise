<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransaksiResource;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TransaksiController extends Controller
{
    protected $urlPetani = 'http://127.0.0.1:8000/api/petani';
    protected $urlProduk = 'http://127.0.0.1:8001/api/produk';

    public function index()
    {
        $transaksi = Transaksi::all();

        return response()->json([
            'message' => 'Data transaksi berhasil diambil',
            'data' => TransaksiResource::collection($transaksi)
        ], 200);
    }

    public function show($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        return response()->json([
            'message' => 'Detail transaksi berhasil diambil',
            'data' => new TransaksiResource($transaksi)
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'petani_id' => 'required|integer',
            'tanggal' => 'nullable|date',
            'produk_id' => 'required|integer',
            'jumlah' => 'required|integer|min:1',
        ]);

        // Ambil data petani
        $petani = Http::get($this->urlPetani . '/' . $request->petani_id);

        if ($petani->failed()) {
            // Log error atau kembalikan response error yang lebih spesifik
            return response()->json(['message' => 'Petani tidak ditemukan atau ada masalah dengan API Petani'], 500);
        }

        // Ambil data produk
        $produk = Http::get($this->urlProduk . '/' . $request->produk_id);

        if ($produk->failed()) {
            // Log error atau kembalikan response error yang lebih spesifik
            return response()->json(['message' => 'Produk tidak ditemukan atau ada masalah dengan API Produk'], 500);
        }

        $produkData = $produk->json();

        if (!isset($produkData['data']['stok'])) {
            return response()->json(['message' => 'Data stok tidak tersedia dalam respons produk'], 500);
        }
        
        $stok = $produkData['data']['stok'];
        $harga = $produkData['data']['harga'];
        
        if ($stok < $request->jumlah) {
            return response()->json(['message' => 'Stok tidak cukup'], 400);
        }
        
        $totalHarga = $harga * $request->jumlah;
        

        // Simpan transaksi
        $transaksi = Transaksi::create([
            'petani_id' => $request->petani_id,
            'tanggal' => $request->tanggal ?? now(),
            'produk_id' => $request->produk_id,
            'jumlah' => $request->jumlah,
            'total_harga' => $totalHarga,
        ]);

        $response = Http::post($this->urlProduk . '/' . $request->produk_id . '/kurangi-stok', [
            'jumlah' => $request->jumlah
        ]);

        if ($response->failed()) {
            return response()->json(['message' => 'Gagal mengurangi stok produk'], 500);
        }


        return response()->json([
            'message' => 'Transaksi berhasil dibuat',
            'data' => new TransaksiResource($transaksi)
        ], 201);
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        $transaksi->delete();

        return response()->json(['message' => 'Transaksi berhasil dihapus'], 200);
    }

    public function byPetani($id)
    {
        $data = Transaksi::where('petani_id', $id)->get();

        return response()->json([
            'message' => 'Daftar transaksi berdasarkan petani berhasil diambil',
            'data' => TransaksiResource::collection($data)
        ], 200);
    }
}
