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
        // Only show prescriptions that are paid (diproses) - ready for pickup
        $examinations = Examination::with([
            'patient',
            'doctor',
            'prescriptions' => function($q) {
                $q->with('medicine')->orderBy('created_at', 'asc');
            },
            'doctorPayment'
        ])
            ->whereHas('prescriptions', function($q) {
                $q->whereIn('status', ['diproses', 'selesai']);
            })
            ->whereHas('doctorPayment', function($q) {
                $q->where('status', 'lunas');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pharmacy.index', compact('examinations'));
    }

    public function complete($id)
    {
        $prescription = Prescription::with('examination')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Reduce stock
            $medicine = Medicine::findOrFail($prescription->medicine_id);
            $medicine->decrement('stock', $prescription->qty);

            // Mark as completed
            $prescription->update(['status' => 'selesai']);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyiapkan obat: ' . $e->getMessage());
        }

        return redirect()->route('pharmacy.index')
            ->with('success', "{$prescription->medicine->name} x{$prescription->qty} sudah diserahkan ke pasien");
    }
}