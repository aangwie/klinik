<?php

namespace App\Http\Controllers;

use App\Models\DoctorPayment;
use App\Models\PharmacySale;
use App\Models\Prescription;
use App\Models\Medicine;
use App\Models\Examination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        $doctorPayments = DoctorPayment::with([
            'patient',
            'examination.prescriptions.medicine'
        ])
            ->whereDate('created_at', now()->format('Y-m-d'))
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($doctorPayments as $payment) {
            $medicineTotal = 0;
            if ($payment->examination && $payment->examination->prescriptions) {
                foreach ($payment->examination->prescriptions as $pres) {
                    if ($pres->medicine) {
                        $medicineTotal += $pres->qty * $pres->medicine->selling_price;
                    }
                }
            }
            $payment->medicine_total = $medicineTotal;
            $payment->grand_total = $payment->total + $medicineTotal;
        }

        return view('payment.index', compact('doctorPayments'));
    }

    public function processTotalPayment(Request $request)
    {
        $validated = $request->validate([
            'doctor_payment_id' => 'required|exists:doctor_payments,id',
            'payment_method' => 'required|in:tunai,qris,transfer,debit',
        ]);

        DB::beginTransaction();
        try {
            $doctorPayment = DoctorPayment::findOrFail($validated['doctor_payment_id']);
            $invoiceNumber = 'INV/TOTAL/' . now()->format('Ymd') . '/' . str_pad($doctorPayment->id, 4, '0', STR_PAD_LEFT);

            $doctorPayment->update([
                'payment_method' => $validated['payment_method'],
                'status' => 'lunas',
                'invoice_number' => $invoiceNumber,
            ]);

            // After payment, set prescriptions to 'diproses' so pharmacy can process
            $examinationId = $doctorPayment->examination_id;
            Prescription::where('examination_id', $examinationId)
                ->where('status', 'menunggu')
                ->update(['status' => 'diproses']);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }

        return redirect()->route('payment.index')
            ->with('success', 'Pembayaran total berhasil. Silakan serahkan struk ke apoteker.');
    }

    public function printStruk($id)
    {
        $payment = DoctorPayment::with([
            'patient',
            'examination.prescriptions.medicine'
        ])->findOrFail($id);

        $medicineTotal = 0;
        $medicineDetails = [];
        if ($payment->examination && $payment->examination->prescriptions) {
            foreach ($payment->examination->prescriptions as $pres) {
                if ($pres->medicine) {
                    $subtotal = $pres->qty * $pres->medicine->selling_price;
                    $medicineTotal += $subtotal;
                    $medicineDetails[] = (object)[
                        'name' => $pres->medicine->name,
                        'qty' => $pres->qty,
                        'price' => $pres->medicine->selling_price,
                        'subtotal' => $subtotal,
                        'instruction' => $pres->instruction ?? '-',
                    ];
                }
            }
        }
        $payment->medicine_total = $medicineTotal;
        $payment->medicine_details = $medicineDetails;
        $payment->grand_total = $payment->total + $medicineTotal;

        return view('payment.struk', compact('payment'));
    }

    public function getDetail($id)
    {
        $payment = DoctorPayment::with([
            'patient',
            'examination.prescriptions.medicine'
        ])->findOrFail($id);

        $medicines = [];
        $medicineTotal = 0;
        if ($payment->examination && $payment->examination->prescriptions) {
            foreach ($payment->examination->prescriptions as $pres) {
                if ($pres->medicine) {
                    $subtotal = $pres->qty * $pres->medicine->selling_price;
                    $medicineTotal += $subtotal;
                    $medicines[] = [
                        'name' => $pres->medicine->name,
                        'qty' => $pres->qty,
                        'price' => $pres->medicine->selling_price,
                        'subtotal' => $subtotal,
                    ];
                }
            }
        }

        return response()->json([
            'patient' => [
                'name' => $payment->patient->name ?? '-',
                'medical_record_number' => $payment->patient->medical_record_number ?? '-',
            ],
            'consultation_fee' => (int) $payment->consultation_fee,
            'action_fee' => (int) $payment->action_fee,
            'total' => (int) $payment->total,
            'actions' => $payment->examination->actions ?? null,
            'medicines' => $medicines,
            'medicine_total' => $medicineTotal,
            'grand_total' => $payment->total + $medicineTotal,
            'status' => $payment->status,
            'payment_method' => $payment->payment_method,
            'invoice_number' => $payment->invoice_number,
        ]);
    }

    public function getEditFee($id)
    {
        $payment = DoctorPayment::findOrFail($id);
        return response()->json($payment);
    }

    public function updateDoctorPaymentFee(Request $request, $id)
    {
        $payment = DoctorPayment::findOrFail($id);

        $validated = $request->validate([
            'consultation_fee' => 'required|integer|min:0',
            'action_fee' => 'required|integer|min:0',
        ]);

        $total = $validated['consultation_fee'] + $validated['action_fee'];

        $payment->update([
            'consultation_fee' => $validated['consultation_fee'],
            'action_fee' => $validated['action_fee'],
            'total' => $total,
        ]);

        return redirect()->route('payment.index')
            ->with('success', 'Biaya jasa dokter berhasil diperbarui');
    }
}