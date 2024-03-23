<?php

namespace App\Http\Controllers;

use App\Models\transaksi;
use App\Http\Requests\StoretransaksiRequest;
use App\Http\Requests\UpdatetransaksiRequest;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transaksis = transaksi::all();
        return response()->json(['transactions' => $transaksis], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoretransaksiRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoretransaksiRequest $request)
    {
        $validatedData = $request->validate([
            'type' => 'required',
            'date' => 'required|date',
            'qty' => 'required|numeric',
            'price' => 'required|numeric',
        ]);

        $qty = $validatedData['type'] == 'Pembelian' ? $validatedData['qty'] * 1 : $validatedData['qty'] * -1;

        $cost = $validatedData['type'] == 'Pembelian' ? $validatedData['price'] : $this->getHPP();
    
        $totalCost = $validatedData['qty'] * $cost;

        $qtyBalance = $this->getPreviousQtyBalance() + $qty;

        $valueBalance = $this->getPreviousValueBalance() + $totalCost;

        $hpp = $qtyBalance != 0 ? $valueBalance / $qtyBalance : 0;

        if($qtyBalance >= 0){
            transaksi::create([
                'description' => $validatedData['type'],
                'date' => $validatedData['date'],
                'qty' => $validatedData['qty'],
                'cost' => $cost,
                'price' => $validatedData['price'],
                'total_cost' => $totalCost,
                'qty_balance' => $qtyBalance,
                'value_balance' => $valueBalance,
                'hpp' => $hpp,
            ]);

            return response()->json(['message' => 'transaksi created successfully'], 201);
        }else{
            return response()->json(['message' => 'transaksi Fail, Stock not available'], 500);
        }
    }

    private function getHPP()
    {
        $latestPenjualan = transaksi::latest()->first();
        return $latestPenjualan ? $latestPenjualan->hpp : 0;
    }

    private function getPreviousQtyBalance()
    {
        $qty = transaksi::latest()->first();
        return $qty ? $qty->qty_balance: 0;
    }

    private function getPreviousValueBalance()
    {
        $vBalance = transaksi::latest()->first();   
        return $vBalance ? $vBalance->total_cost : 0;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\transaksi  $transaksi
     * @return \Illuminate\Http\Response
     */
    public function show(transaksi $transaksi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\transaksi  $transaksi
     * @return \Illuminate\Http\Response
     */
    public function edit(transaksi $transaksi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatetransaksiRequest  $request
     * @param  \App\Models\transaksi  $transaksi
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatetransaksiRequest $request, transaksi $transaksi,$id)
    {
        $validatedData = $request->validate([
            'type' => 'required',
            'date' => 'required|date',
            'qty' => 'required|numeric',
            'price' => 'required|numeric',
        ]);

        $transaksi = transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'transaksi not found'], 404);
        }
        
        $qty = $validatedData['type'] == 'Pembelian' ? $validatedData['qty'] * 1 : $validatedData['qty'] * -1;

        $cost = $validatedData['type'] == 'Pembelian' ? $validatedData['price'] : $this->getHPP($validatedData['date']);

        $totalCost = $validatedData['qty'] * $cost;

        $qtyBalance = $this->getPreviousQtyBalance() + $qty;

        $valueBalance = $this->getPreviousValueBalance() + $totalCost;

        $hpp = $qtyBalance != 0 ? $valueBalance / $qtyBalance : 0;

        if($qtyBalance >= 0){
            $transaksi->update([
                'description' => $validatedData['type'],
                'date' => $validatedData['date'],
                'qty' => $validatedData['qty'],
                'cost' => $cost,
                'price' => $validatedData['price'],
                'total_cost' => $totalCost,
                'qty_balance' => $qtyBalance,
                'value_balance' => $valueBalance,
                'hpp' => $hpp,
            ]);

            return response()->json(['message' => 'transaksi updated successfully'], 200);
        }else{
            return response()->json(['message' => 'transaksi Fail, Stock not available'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\transaksi  $transaksi
     * @return \Illuminate\Http\Response
     */
    public function destroy(transaksi $transaksi,$id)
    {
        $transaksi = transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'transaksi not found'], 404);
        }

        $qtyBalance = $transaksi['qty_balance'] - $transaksi['qty'];

        if($qtyBalance >= 0){
            $transaksi->delete();
            return response()->json(['message' => 'transaksi deleted successfully'], 200);
        }else{
            return response()->json(['message' => 'transaksi Fail, Stock not available'], 500);
        }
    }
}
