<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransaksiResource;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TransaksiController extends Controller
{
    private function fetchServiceList($key, $url)
    {
        return Cache::remember($key, 60, function () use ($url, $key) {
            try {
                $start = microtime(true);
                $response = Http::retry(1, 100)->timeout(5)->get($url);
                $duration = round(microtime(true) - $start, 2);
                \Log::info("{$key} response time: {$duration}s");

                return $response->successful()
                    ? collect($response->json('data') ?? [])->keyBy('id')
                    : collect([]);
            } catch (\Exception $e) {
                \Log::error("Service {$key} error: " . $e->getMessage());
                return collect([]);
            }
        });
    }

    public function index()
    {
        $transaksi = Transaksi::all();
        $petaniList = $this->fetchServiceList('petani_list', env('PETANI_SERVICE_URL'));
        $produkList = $this->fetchServiceList('produk_list', env('PRODUK_SERVICE_URL'));

        $data = $transaksi->map(fn($item) => (new TransaksiResource($item))->setExtra($petaniList, $produkList));

        return response()->json(['message' => 'Data transaksi berhasil diambil', 'data' => $data], 200);
    }

    public function show($id)
    {
        $transaksi = Transaksi::find($id);
        if (!$transaksi)
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);

        $petani = null;
        $produk = null;

        try {
            $petaniRes = Http::retry(1, 100)->timeout(5)->get(env('PETANI_SERVICE_URL') . '/' . $transaksi->petani_id);
            $petani = $petaniRes->successful() ? $petaniRes->json('data') : null;
        } catch (\Exception $e) {
            \Log::error("Fetch petani error: " . $e->getMessage());
        }

        try {
            $produkRes = Http::retry(1, 100)->timeout(5)->get(env('PRODUK_SERVICE_URL') . '/' . $transaksi->produk_id);
            $produk = $produkRes->successful() ? $produkRes->json('data') : null;
        } catch (\Exception $e) {
            \Log::error("Fetch produk error: " . $e->getMessage());
        }

        $petaniList = collect([$transaksi->petani_id => $petani ?? []]);
        $produkList = collect([$transaksi->produk_id => $produk ?? []]);

        return response()->json([
            'message' => 'Detail transaksi berhasil diambil',
            'data' => (new TransaksiResource($transaksi))->setExtra($petaniList, $produkList)
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

        $petani = null;
        try {
            $petaniRes = Http::retry(1, 100)->timeout(5)->get(env('PETANI_SERVICE_URL') . '/' . $request->petani_id);
            $petani = $petaniRes->successful() ? $petaniRes->json('data') : null;
        } catch (\Exception $e) {
            \Log::error("Fetch petani error: " . $e->getMessage());
        }

        if (!$petani) {
            return response()->json(['message' => 'Data Petani tidak ditemukan'], 500);
        }

        $produk = null;
        try {
            $produkRes = Http::retry(1, 100)->timeout(5)->get(env('PRODUK_SERVICE_URL') . '/' . $request->produk_id);
            $produk = $produkRes->successful() ? $produkRes->json('data') : null;
        } catch (\Exception $e) {
            \Log::error("Fetch produk error: " . $e->getMessage());
        }

        if (!$produk) {
            return response()->json(['message' => 'Data produk tidak valid'], 500);
        }

        if (!isset($produk['stok']) || $produk['stok'] < $request->jumlah) {
            return response()->json(['message' => 'Stok tidak cukup'], 400);
        }

        $totalHarga = $produk['harga'] * $request->jumlah;

        $transaksi = Transaksi::create([
            'petani_id' => $request->petani_id,
            'tanggal' => $request->tanggal ?? now(),
            'produk_id' => $request->produk_id,
            'jumlah' => $request->jumlah,
            'total_harga' => $totalHarga,
        ]);

        $response = Http::post(env('PRODUK_SERVICE_URL') . '/' . $request->produk_id . '/kurangi-stok', [
            'jumlah' => $request->jumlah
        ]);

        if ($response->failed()) {
            return response()->json(['message' => 'Gagal mengurangi stok produk'], 500);
        }

        $petaniList = collect([$request->petani_id => $petani]);
        $produkList = collect([$request->produk_id => $produk]);

        return response()->json([
            'message' => 'Transaksi berhasil dibuat',
            'data' => (new TransaksiResource($transaksi))->setExtra($petaniList, $produkList)
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'petani_id' => 'required|integer',
            'tanggal' => 'nullable|date',
            'produk_id' => 'required|integer',
            'jumlah' => 'required|integer|min:1',
        ]);

        $transaksi = Transaksi::find($id);
        if (!$transaksi)
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);

        $petani = null;
        $produk = null;

        try {
            $petaniRes = Http::retry(1, 100)->timeout(5)->get(env('PETANI_SERVICE_URL') . '/' . $request->petani_id);
            $petani = $petaniRes->successful() ? $petaniRes->json('data') : null;
        } catch (\Exception $e) {
            \Log::error("Fetch petani error: " . $e->getMessage());
        }

        if (!$petani) {
            return response()->json(['message' => 'Masyarakat ID tidak ditemukan'], 500);
        }

        try {
            $produkRes = Http::retry(1, 100)->timeout(5)->get(env('PRODUK_SERVICE_URL') . '/' . $request->produk_id);
            $produk = $produkRes->successful() ? $produkRes->json('data') : null;
        } catch (\Exception $e) {
            \Log::error("Fetch produk error: " . $e->getMessage());
        }

        if (!$produk) {
            return response()->json(['message' => 'Data produk tidak valid'], 500);
        }

        $selisihJumlah = $request->jumlah - $transaksi->jumlah;
        if ($selisihJumlah > 0 && $produk['stok'] < $selisihJumlah) {
            return response()->json(['message' => 'Stok tidak cukup untuk update jumlah'], 400);
        }

        if ($selisihJumlah !== 0) {
            $response = Http::post(env('PRODUK_SERVICE_URL') . '/' . $request->produk_id . '/kurangi-stok', [
                'jumlah' => $selisihJumlah
            ]);
            if ($response->failed()) {
                return response()->json(['message' => 'Gagal memperbarui stok produk'], 500);
            }
        }

        $transaksi->update([
            'petani_id' => $request->petani_id,
            'tanggal' => $request->tanggal ?? now(),
            'produk_id' => $request->produk_id,
            'jumlah' => $request->jumlah,
            'total_harga' => $produk['harga'] * $request->jumlah,
        ]);

        $petaniList = collect([$request->petani_id => $petani]);
        $produkList = collect([$request->produk_id => $produk]);

        return response()->json([
            'message' => 'Transaksi berhasil diperbarui',
            'data' => (new TransaksiResource($transaksi))->setExtra($petaniList, $produkList)
        ], 200);
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::find($id);
        if (!$transaksi)
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);

        $transaksi->delete();
        return response()->json(['message' => 'Transaksi berhasil dihapus'], 200);
    }

    public function byPetani($id)
    {
        $transaksi = Transaksi::where('petani_id', $id)->get();
        $produkList = $this->fetchServiceList('produk_list', env('PRODUK_SERVICE_URL'));

        $petani = null;
        try {
            $petaniRes = Http::retry(1, 100)->timeout(5)->get(env('PETANI_SERVICE_URL') . '/' . $id);
            $petani = $petaniRes->successful() ? $petaniRes->json('data') : null;
        } catch (\Exception $e) {
            \Log::error("Fetch petani error: " . $e->getMessage());
        }

        $petaniList = collect([$id => $petani ?? []]);

        $data = $transaksi->map(fn($item) => (new TransaksiResource($item))->setExtra($petaniList, $produkList));

        return response()->json([
            'message' => 'Daftar transaksi berdasarkan petani berhasil diambil',
            'data' => $data
        ], 200);
    }
}
