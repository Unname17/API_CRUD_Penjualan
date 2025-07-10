<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Models\Barang;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {

        $orders = Order::with(['customer', 'barang'])->get();
        return response()->json($orders, 200);
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
        'id_customer' => 'required|string|max:255',
        'id_barang' => 'required|string|max:255',
        'order_date' => 'required|date',
        'jumlah_barang' => 'required|integer|min:1',
        'total' => 'required|integer',
    ]);

    DB::beginTransaction();

    try {
        // Ambil data stok berdasarkan id_barang
        $barang = Barang::where('id', $request->id_barang)->lockForUpdate()->first();



        if (!$barang) {
            return response()->json(['message' => 'Barang barang tidak ditemukan.'], 404);
        }

        if ($barang->jumlah < $request->jumlah_barang) {
            return response()->json(['message' => 'Barang barang tidak mencukupi.'], 400);
        }

        // Kurangi jumlah barang
        $barang->decrement('jumlah', $request->jumlah_barang);

        // Buat order
        $order = Order::create([
            'id_customer' => $request->id_customer,
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
    DB::beginTransaction();

    try {
        $order = Order::findOrFail($id);

        $request->validate([
            'id_customer' => 'sometimes|string|max:255',
            'id_barang' => 'sometimes|string|max:255',
            'order_date' => 'sometimes|date',
            'jumlah_barang' => 'sometimes|integer|min:1',
            'total' => 'sometimes|integer',
        ]);

        $data = $request->only(['id_customer', 'id_barang', 'order_date', 'jumlah_barang', 'total']);

        // Cek apakah jumlah_barang diubah
        if (isset($data['jumlah_barang'])) {
            // Ambil barang dengan locking untuk menghindari race condition
            $barang = Barang::where('id', $order->id_barang)->lockForUpdate()->first();

            if (!$barang) {
                DB::rollBack();
                return response()->json(['message' => 'Barang tidak ditemukan.'], 404);
            }

            $jumlah_lama = $order->jumlah_barang;
            $jumlah_baru = $data['jumlah_barang'];
            $selisih = $jumlah_baru - $jumlah_lama;

            // Jika jumlah bertambah, pastikan stok mencukupi
            if ($selisih > 0) {
                if ($barang->jumlah < $selisih) {
                    DB::rollBack();
                    return response()->json(['message' => 'Stok barang tidak mencukupi untuk update.'], 400);
                }

                $barang->decrement('jumlah', $selisih);
            } elseif ($selisih < 0) {
                // Jika dikurangi, kembalikan stok
                $barang->increment('jumlah', abs($selisih));
            }
        }

        // Update data order
        $order->update($data);

        DB::commit();

        return response()->json([
            'message' => $order->wasChanged()
                ? 'Data order dan stok berhasil diupdate.'
                : 'Tidak ada perubahan pada data order.',
            'data' => $order
        ], 200);
    } catch (ModelNotFoundException $e) {
        DB::rollBack();
        return response()->json(['message' => 'Order tidak ditemukan'], 404);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Gagal memperbarui order.',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function destroy($id): JsonResponse
{
    DB::beginTransaction();

    try {
        $order = Order::findOrFail($id);

        // Ambil barang terkait
        $barang = Barang::where('id', $order->id_barang)->lockForUpdate()->first();

        if (!$barang) {
            DB::rollBack();
            return response()->json(['message' => 'Barang tidak ditemukan.'], 404);
        }

        // Tambahkan kembali jumlah barang
        $barang->increment('jumlah', $order->jumlah_barang);

        // Hapus order
        $order->delete();

        DB::commit();

        return response()->json(['message' => 'Order berhasil dihapus dan stok barang dikembalikan.'], 200);
    } catch (ModelNotFoundException $e) {
        DB::rollBack();
        return response()->json(['message' => 'Order tidak ditemukan.'], 404);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Gagal menghapus order.',
            'error' => $e->getMessage()
        ], 500);
    }
}

}