<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StockController extends Controller
{
    public function index(): JsonResponse
    {

        $dataStock = Stock::all();
        return response()->json($dataStock, 200);
    }

    public function show($id): JsonResponse
    {
        try {
            $stock = Stock::findOrFail($id);
            return response()->json($stock, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Stock tidak ditemukan'], 404);
        }
    }

    // Menambahkan user baru
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'id_barang' => 'required|string|max:255',
            'limit' => 'required|integer|max:255',
        ]);

        $stock = Stock::create([
            'id_barang' => $request->id_barang,
            'limit' => $request->limit,
        ]);


        return response()->json([
            'message' => 'Data stock berhasil ditambahkan.',
            'data' => $stock
        ], 201);
    }

      // Mengupdate data user
      public function update(Request $request, $id): JsonResponse
      {
          try {
              $stock = Stock::findOrFail($id);
  
              $request->validate([
                'id_barang' => 'sometimes|string|max:255',
                'limit' => 'sometimes|integer|max:255',
            ]);
  
              // Hanya update field yang dikirim
              $data = $request->only(['barang_id', 'limit']);

              $stock->update($data);
              
  
              return response()->json([
                  'message' => $stock->wasChanged()
                      ? 'Data stock berhasil diupdate.'
                      : 'Tidak ada perubahan pada data stock.',
                  'data' => $stock
              ], 200);
          } catch (ModelNotFoundException $e) {
              return response()->json(['message' => 'Stock tidak ditemukan'], 404);
          }
      }
  
      public function destroy($id): JsonResponse
      {
          try {
              $stock = Stock::findOrFail($id);
              $stock->delete();
  
              return response()->json(['message' => 'Stock berhasil dihapus.']);
          } catch (ModelNotFoundException $e) {
              return response()->json(['message' => 'Stock tidak ditemukan.'], 404);
          }
      }
}