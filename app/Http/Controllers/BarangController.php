<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BarangController extends Controller
{
    public function index(): JsonResponse
    {

        $dataBarang = Barang::all();
        return response()->json($dataBarang, 200);
    }

    public function show($id): JsonResponse
    {
        try {
            $barang = Barang::findOrFail($id);
            return response()->json($barang, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }
    }

    // Menambahkan user baru
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer',
            'harga' => 'required|numeric|min:0',
        ]);

        $barang = Barang::create([
            'nama_barang' => $request->nama_barang,
            'jumlah' => $request->jumlah,
            'harga' => $request->harga,
        ]);


        return response()->json([
            'message' => 'Barang berhasil ditambahkan.',
            'data' => $barang
        ], 201);
    }

      // Mengupdate data user
      public function update(Request $request, $id): JsonResponse
      {
          try {
              $barang = Barang::findOrFail($id);
  
              $request->validate([
                'nama_barang' => 'sometimes|string|max:255',
                'harga' => 'sometimes|integer',
                'jumlah' => 'sometimes|numeric|min:0',
            ]);
  
              // Hanya update field yang dikirim
              $data = $request->only(['nama_barang', 'harga', 'jumlah']);

              $barang->update($data);
              
  
              return response()->json([
                  'message' => $barang->wasChanged()
                      ? 'Data barang berhasil diupdate.'
                      : 'Tidak ada perubahan pada data barang.',
                  'data' => $barang
              ], 200);
          } catch (ModelNotFoundException $e) {
              return response()->json(['message' => 'Data barang tidak ditemukan'], 404);
          }
      }
  
      public function destroy($id): JsonResponse
      {
          try {
              $barang = Barang::findOrFail($id);
              $barang->delete();
  
              return response()->json(['message' => 'Data barang berhasil dihapus.']);
          } catch (ModelNotFoundException $e) {
              return response()->json(['message' => 'Data barang tidak ditemukan.'], 404);
          }
      }
}