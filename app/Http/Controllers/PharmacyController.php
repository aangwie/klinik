<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\PharmacySale;
use App\Models\Medicine;
use App\Models\Examination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PharmacyController extends Controller
{
    public function index()
    {
        // Get all examinations that have prescriptions waiting/processing
        $examinations = Examination::with([
            'patient',
            'doctor',
            'prescriptions' => function($q) {
                $q->with('medicine')->orderBy('created_at', 'asc');
            },
            'doctorPayment'
        ])
            ->whereHas('prescriptions', function($q) {
                $q->whereIn('status', ['menunggu', 'diproses']);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Flag: can only process if doctor payment is lunas OR for same patient no pending doctor payment
        foreach ($examinations as $exam) {
            $exam->can_process = true;
            if ($exam->doctorPayment && $exam->doctorPayment->status == 'menunggu') {
                $exam->can_process = false;
            }
        }

        return view('pharmacy.index', compact('examinations'));
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
                'status' => 'menunggu',
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