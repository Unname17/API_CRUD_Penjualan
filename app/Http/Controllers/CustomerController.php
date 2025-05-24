<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CustomerController extends Controller
{
    public function index(): JsonResponse
    {

        $dataUCustomer = Customer::all();
        return response()->json($dataUCustomer, 200);
    }

    public function show($id): JsonResponse
    {
        try {
            $customer = Customer::findOrFail($id);
            return response()->json($customer, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Customer tidak ditemukan'], 404);
        }
    }

    // Menambahkan user baru
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'no_hp' => 'required|max:15|min:8|unique:customer,no_hp',
        ]);

        $customer = Customer::create([
            'customer_name' => $request->customer_name,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
        ]);


        return response()->json([
            'message' => 'Akun customer berhasil ditambahkan.',
            'data' => $customer
        ], 201);
    }

      // Mengupdate data user
      public function update(Request $request, $id): JsonResponse
      {
          try {
              $customer = Customer::findOrFail($id);
  
              $request->validate([
                'customer_name' => 'sometimes|string|max:255',
                'alamat' => 'sometimes|string|max:255',
                'no_hp' => 'sometimes|max:15|min:8|unique:customer,no_hp',
            ]);
  
              // Hanya update field yang dikirim
              $data = $request->only(['customer_name', 'alamat', 'no_hp']);

              $customer->update($data);
              
  
              return response()->json([
                  'message' => $customer->wasChanged()
                      ? 'Akun customer berhasil diupdate.'
                      : 'Tidak ada perubahan pada data customer.',
                  'data' => $customer
              ], 200);
          } catch (ModelNotFoundException $e) {
              return response()->json(['message' => 'Akun tidak ditemukan'], 404);
          }
      }
  
      public function destroy($id): JsonResponse
      {
          try {
              $customer = Customer::findOrFail($id);
              $customer->delete();
  
              return response()->json(['message' => 'Customer berhasil dihapus.']);
          } catch (ModelNotFoundException $e) {
              return response()->json(['message' => 'Customer tidak ditemukan.'], 404);
          }
      }
}