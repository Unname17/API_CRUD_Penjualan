<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Models\Stock;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {

        $dataOrder = Order::all();
        return response()->json($dataOrder, 200);
    }

    public function show($id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);
            return response()->json($order, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Orders tidak ditemukan'], 404);
        }
    }

    // Menambahkan user baru
public function store(Request $request): JsonResponse
{
    $request->validate([
        'customer_id' => 'required|string|max:255',
        'id_barang' => 'required|string|max:255',
        'order_date' => 'required|date',
        'jumlah_barang' => 'required|integer|min:1',
        'total' => 'required|integer',
    ]);

    DB::beginTransaction();

    try {
        // Ambil data stok berdasarkan id_barang
        $stock = Stock::where('id_barang', $request->id_barang)->first();

        if (!$stock) {
            return response()->json(['message' => 'Stock barang tidak ditemukan.'], 404);
        }

        if ($stock->limit < $request->jumlah_barang) {
            return response()->json(['message' => 'Stok barang tidak mencukupi.'], 400);
        }

        // Kurangi stok limit
        $stock->decrement('limit', $request->jumlah_barang);

        // Buat order
        $order = Order::create([
            'customer_id' => $request->customer_id,
            'id_barang' => $request->id_barang,
            'order_date' => $request->order_date,
            'jumlah_barang' => $request->jumlah_barang,
            'total' => $request->total,
        ]);

        DB::commit();

        return response()->json([
            'message' => 'Data order berhasil ditambahkan dan stok dikurangi.',
            'data' => $order
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Gagal membuat order.',
            'error' => $e->getMessage()
        ], 500);
    }
}


    // Mengupdate data user
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);

            $request->validate([
                'customer_id' => 'sometimes|string|max:255',
                'id_barang' => 'sometimes|string|max:255',
                'order_date' => 'sometimes|string|date',
                'jumlah_barang' => 'sometimes|integer',
                'total' => 'sometimes|integer',
            ]);

            // Hanya update field yang dikirim
            $data = $request->only(['customer_id', 'id_barang', 'order_date', 'jumlah_barang', 'total']);

            $order->update($data);


            return response()->json([
                'message' => $order->wasChanged()
                    ? 'Data order berhasil diupdate.'
                    : 'Tidak ada perubahan pada data stock.',
                'data' => $order
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);
            $order->delete();

            return response()->json(['message' => 'Order berhasil dihapus.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Order tidak ditemukan.'], 404);
        }
    }
}