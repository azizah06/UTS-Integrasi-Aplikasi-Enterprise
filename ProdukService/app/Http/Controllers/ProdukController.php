<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Resources\ProdukResource;
use Illuminate\Validation\ValidationException;

class ProdukController extends Controller
{
    protected $urlMitra = 'http://127.0.0.1:8003/api/mitra';

    public function index()
    {
        $produks = Produk::all();

        return response()->json([
            'message' => 'Data produk berhasil diambil',
            'data' => ProdukResource::collection($produks)
        ], 200);
    }

    /**
     * Tampilkan satu produk berdasarkan ID.
     */
    public function show($id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json([
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail produk berhasil diambil',
            'data' => new ProdukResource($produk)
        ], 200);
    }

    /**
     * Tambah produk baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'kategori' => 'required|in:bibit,pupuk,alat tani',
            'stok' => 'required|integer|min:0',
            'mitra_id' => 'required|integer'
        ]);

        // Ambil data petani
        $mitra = Http::get($this->urlMitra . '/' . $request->mitra_id);

        if ($mitra->failed()) {
            // Log error atau kembalikan response error yang lebih spesifik
            return response()->json(['message' => 'Mitra tidak ditemukan atau ada masalah dengan API Mitra'], 500);
        }

        $produk = Produk::create($validated);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan',
            'data' => new ProdukResource($produk)
        ], 201);
    }

    /**
     * Update data produk.
     */
    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json([
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'kategori' => 'required|in:bibit,pupuk,alat tani',
            'stok' => 'required|integer|min:0',
            'mitra_id' => 'required|integer'
        ]);

        // Ambil data petani
        $mitra = Http::post($this->urlMitra . '/' . $request->mitra_id);

        if ($mitra->failed()) {
            // Log error atau kembalikan response error yang lebih spesifik
            return response()->json(['message' => 'Mitra tidak ditemukan atau ada masalah dengan API Petani'], 500);
        }

        $produk->update($validated);

        return response()->json([
            'message' => 'Produk berhasil diperbarui',
            'data' => new ProdukResource($produk)
        ], 200);
    }

    /**
     * Hapus produk.
     */
    public function destroy($id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json([
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        $produk->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus'
        ], 200);
    }

    /**
     * Kurangi stok produk.
     */
    public function kurangiStok(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1'
        ]);

        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json([
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        if ($produk->stok < $request->jumlah) {
            return response()->json([
                'message' => 'Stok tidak cukup'
            ], 400);
        }

        $produk->stok -= $request->jumlah;
        $produk->save();

        return response()->json([
            'message' => 'Stok produk berhasil dikurangi',
            'data' => new ProdukResource($produk)
        ], 200);
    }
}
