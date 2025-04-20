<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Http\Resources\MitraResource;
use Illuminate\Http\Request;

class MitraController extends Controller
{
    // GET: /api/mitra
    public function index()
    {
        $mitras = Mitra::all();
        return response()->json([
            'message' => 'Data mitra berhasil diambil',
            'data' => MitraResource::collection($mitras)
        ]);
    }

    // GET: /api/mitra/{id}
    public function show($id)
    {
        $mitra = Mitra::find($id);
        if (!$mitra) {
            return response()->json(['message' => 'Mitra tidak ditemukan'], 404);
        }

        return response()->json([
            'message' => 'Detail mitra berhasil diambil',
            'data' => new MitraResource($mitra)
        ]);
    }

    // POST: /api/mitra
    public function store(Request $request)
    {
        $request->validate([
            'nama_mitra' => 'required|string|max:255',
            'alamat_mitra' => 'required|string',
            'no_telp_mitra' => 'required|string|max:20',
        ]);

        $mitra = Mitra::create($request->all());

        return response()->json([
            'message' => 'Mitra berhasil ditambahkan',
            'data' => new MitraResource($mitra)
        ], 201);
    }

    // PUT: /api/mitra/{id}
    public function update(Request $request, $id)
    {
        $mitra = Mitra::find($id);
        if (!$mitra) {
            return response()->json(['message' => 'Mitra tidak ditemukan'], 404);
        }

        $request->validate([
            'nama_mitra' => 'required|string|max:255',
            'alamat_mitra' => 'required|string',
            'no_telp_mitra' => 'required|string|max:20',
        ]);

        $mitra->update($request->all());

        return response()->json([
            'message' => 'Mitra berhasil diperbarui',
            'data' => new MitraResource($mitra)
        ]);
    }

    // DELETE: /api/mitra/{id}
    public function destroy($id)
    {
        $mitra = Mitra::find($id);
        if (!$mitra) {
            return response()->json(['message' => 'Mitra tidak ditemukan'], 404);
        }

        $mitra->delete();

        return response()->json(['message' => 'Mitra berhasil dihapus']);
    }
}
