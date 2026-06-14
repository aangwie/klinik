<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\PharmacySale;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PharmacyController extends Controller
{
    public function index()
    {
        $prescriptions = Prescription::with(['examination.patient', 'examination.doctor', 'medicine'])
            ->whereIn('status', ['menunggu', 'diproses'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pharmacy.index', compact('prescriptions'));
    }

    public function process($id)
    {
        $prescription = Prescription::findOrFail($id);
        $prescription->update(['status' => 'diproses']);

        return redirect()->route('pharmacy.index')
            ->with('success', 'Resep sedang diproses');
    }

    public function complete($id)
    {
        $prescription = Prescription::with('examination')->findOrFail($id);
        
        DB::beginTransaction();
        try {
            $total = $prescription->qty * $prescription->medicine->selling_price;

            PharmacySale::create([
                'prescription_id' => $prescription->id,
                'patient_id' => $prescription->examination->patient_id,
                'total' => $total,
                'status' => 'menunggu_pembayaran',
            ]);

            $prescription->update(['status' => 'menunggu_pembayaran']);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyiapkan obat: ' . $e->getMessage());
        }

        return redirect()->route('pharmacy.index')
            ->with('success', 'Obat siap, menunggu pembayaran');
    }
}