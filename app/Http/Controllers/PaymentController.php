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
        $tab = request()->get('tab', 'total');

        // Doctor payments with prescriptions data
        $doctorPayments = DoctorPayment::with([
            'patient',
            'examination.prescriptions.medicine'
        ])
            ->whereDate('created_at', now()->format('Y-m-d'))
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate medicine costs per doctor payment
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

        $pharmacyPayments = PharmacySale::with(['patient', 'prescription.medicine'])
            ->whereDate('created_at', now()->format('Y-m-d'))
            ->orderBy('created_at', 'desc')
            ->get();

        // All payments (combined for "total" tab)
        $allPayments = collect();

        foreach ($doctorPayments as $dp) {
            if ($dp->status == 'menunggu') {
                $allPayments->push((object)[
                    'id' => $dp->id,
                    'type' => 'doctor',
                    'patient' => $dp->patient,
                    'description' => 'Jasa Dokter' . ($dp->examination && $dp->examination->actions ? ' (' . $dp->examination->actions . ')' : ''),
                    'doctor_fee' => $dp->total,
                    'medicine_total' => $dp->medicine_total ?? 0,
                    'total' => $dp->grand_total ?? $dp->total,
                    'status' => $dp->status,
                    'created_at' => $dp->created_at,
                    'payment_method' => $dp->payment_method,
                    'invoice_number' => $dp->invoice_number,
                    'actions' => $dp->examination->actions ?? null,
                ]);
            }
        }

        foreach ($pharmacyPayments as $pp) {
            if ($pp->status == 'menunggu_pembayaran') {
                $allPayments->push((object)[
                    'id' => $pp->id,
                    'type' => 'pharmacy',
                    'patient' => $pp->patient,
                    'description' => 'Obat: ' . ($pp->prescription->medicine->name ?? '-'),
                    'doctor_fee' => 0,
                    'medicine_total' => $pp->total,
                    'total' => $pp->total,
                    'status' => $pp->status,
                    'created_at' => $pp->created_at,
                    'payment_method' => $pp->payment_method,
                    'receipt_number' => $pp->receipt_number,
                ]);
            }
        }

        $allPayments = $allPayments->sortByDesc('created_at');

        $medicines = Medicine::where('stock', '>', 0)->orderBy('name')->get();

        return view('payment.index', compact('tab', 'doctorPayments', 'pharmacyPayments', 'allPayments', 'medicines'));
    }

    public function processDoctorPayment(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => 'required|exists:doctor_payments,id',
            'payment_method' => 'required|in:tunai,qris,transfer,debit',
            'payment_type' => 'required|in:doctor',
        ]);

        $payment = DoctorPayment::findOrFail($validated['payment_id']);

        $invoiceNumber = 'INV/JD/' . now()->format('Ymd') . '/' . str_pad($payment->id, 4, '0', STR_PAD_LEFT);

        $payment->update([
            'payment_method' => $validated['payment_method'],
            'status' => 'lunas',
            'invoice_number' => $invoiceNumber,
        ]);

        return redirect()->route('payment.index', ['tab' => 'jasa'])
            ->with('success', 'Pembayaran jasa dokter berhasil');
    }

    public function processPharmacyPayment(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => 'required|exists:pharmacy_sales,id',
            'payment_method' => 'required|in:tunai,qris,transfer,debit',
            'payment_type' => 'required|in:pharmacy',
        ]);

        $sale = PharmacySale::findOrFail($validated['payment_id']);

        $receiptNumber = 'RCP/OB/' . now()->format('Ymd') . '/' . str_pad($sale->id, 4, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            $sale->update([
                'payment_method' => $validated['payment_method'],
                'status' => 'lunas',
                'receipt_number' => $receiptNumber,
            ]);

            $prescription = Prescription::findOrFail($sale->prescription_id);
            $prescription->update(['status' => 'selesai']);

            $medicine = Medicine::findOrFail($prescription->medicine_id);
            $medicine->decrement('stock', $prescription->qty);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }

        return redirect()->route('payment.index', ['tab' => 'obat'])
            ->with('success', 'Pembayaran obat berhasil');
    }

    public function processTotalPayment(Request $request)
    {
        $validated = $request->validate([
            'doctor_payment_id' => 'required|exists:doctor_payments,id',
            'payment_method' => 'required|in:tunai,qris,transfer,debit',
        ]);

        DB::beginTransaction();
        try {
            // Process doctor payment
            $doctorPayment = DoctorPayment::findOrFail($validated['doctor_payment_id']);
            $invoiceNumber = 'INV/TOTAL/' . now()->format('Ymd') . '/' . str_pad($doctorPayment->id, 4, '0', STR_PAD_LEFT);

            $doctorPayment->update([
                'payment_method' => $validated['payment_method'],
                'status' => 'lunas',
                'invoice_number' => $invoiceNumber,
            ]);

            // Process all pending pharmacy sales for this patient's examination
            $examinationId = $doctorPayment->examination_id;
            $pendingPrescriptions = Prescription::where('examination_id', $examinationId)
                ->whereIn('status', ['menunggu_pembayaran'])
                ->get();

            foreach ($pendingPrescriptions as $prescription) {
                $pharmacySale = PharmacySale::where('prescription_id', $prescription->id)
                    ->where('status', 'menunggu_pembayaran')
                    ->first();

                if ($pharmacySale) {
                    $receiptNumber = 'RCP/OB/' . now()->format('Ymd') . '/' . str_pad($pharmacySale->id, 4, '0', STR_PAD_LEFT);
                    $pharmacySale->update([
                        'payment_method' => $validated['payment_method'],
                        'status' => 'lunas',
                        'receipt_number' => $receiptNumber,
                    ]);

                    $prescription->update(['status' => 'selesai']);

                    $medicine = Medicine::findOrFail($prescription->medicine_id);
                    $medicine->decrement('stock', $prescription->qty);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pembayaran total: ' . $e->getMessage());
        }

        return redirect()->route('payment.index', ['tab' => 'total'])
            ->with('success', 'Pembayaran total berhasil');
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

        return redirect()->route('payment.index', ['tab' => 'jasa'])
            ->with('success', 'Biaya jasa dokter berhasil diperbarui');
    }

    public function printStruk($type, $id)
    {
        if ($type == 'doctor') {
            $payment = DoctorPayment::with(['patient', 'examination'])->findOrFail($id);
        } elseif ($type == 'pharmacy') {
            $payment = PharmacySale::with(['patient', 'prescription.medicine'])->findOrFail($id);
        } else {
            // Total struk - doctor payment with prescriptions
            $payment = DoctorPayment::with([
                'patient',
                'examination.prescriptions.medicine'
            ])->findOrFail($id);

            // Calculate medicine total
            $medicineTotal = 0;
            $medicineDetails = [];
            if ($payment->examination && $payment->examination->prescriptions) {
                foreach ($payment->examination->prescriptions as $pres) {
                    if ($pres->medicine && $pres->status == 'selesai') {
                        $subtotal = $pres->qty * $pres->medicine->selling_price;
                        $medicineTotal += $subtotal;
                        $medicineDetails[] = (object)[
                            'name' => $pres->medicine->name,
                            'qty' => $pres->qty,
                            'price' => $pres->medicine->selling_price,
                            'subtotal' => $subtotal,
                        ];
                    }
                }
            }
            $payment->medicine_total = $medicineTotal;
            $payment->medicine_details = $medicineDetails;
            $type = 'total';
        }

        return view('payment.struk', compact('payment', 'type'));
    }

    public function getEditFee($id)
    {
        $payment = DoctorPayment::findOrFail($id);
        return response()->json($payment);
    }
}