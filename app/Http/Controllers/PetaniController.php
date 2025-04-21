<?php

namespace App\Http\Controllers;

use App\Models\Petani;
use App\Http\Resources\PetaniResource;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PetaniController extends Controller
{

    public function index()
    {
        $petani = Petani::all();

        return response()->json([
            'message' => 'Data Petani berhasil diambil',
            'data' => PetaniResource::collection($petani)
        ], 200);
    }

    public function show($id)
    {
        $petani = Petani::find($id);

        if (!$petani) {
            return response()->json([
                'message' => 'Data Petani tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail data Petani berhasil diambil',
            'data' => new PetaniResource($petani)
        ], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'nomor_telepon' => 'required|string'
        ]);

        $petani = Petani::create($validatedData);

        return response()->json([
            'message' => 'Data Petani berhasil ditambahkan',
            'data' => new PetaniResource($petani)
        ], 201);
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'nama' => 'required|string|max:255',
                'alamat' => 'required|string',
                'nomor_telepon' => 'required|string'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Data wajib diisi semua',
            ], 422);
        }

        $petani = Petani::find($id);
        if (!$petani) {
            return response()->json([
                'message' => 'Data Petani tidak ditemukan'
            ], 404);
        }

        $petani->update($validatedData);

        return response()->json([
            'message' => 'Data Petani berhasil diperbarui',
            'data' => new PetaniResource($petani)
        ], 200);
    }

    public function destroy($id)
    {
        $petani = Petani::find($id);

        if (!$petani) {
            return response()->json([
                'message' => 'Data Petani tidak ditemukan'
            ], 404);
        }

        $petani->delete();

        return response()->json([
            'message' => 'Data Petani berhasil dihapus'
        ], 200);
    }

    
}
