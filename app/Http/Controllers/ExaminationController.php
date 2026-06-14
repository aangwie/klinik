<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Examination;
use App\Models\Queue;
use App\Models\Medicine;
use App\Models\DoctorPayment;
use App\Models\ServiceAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExaminationController extends Controller
{
    public function index()
    {
        $examinations = Examination::with(['patient', 'doctor'])
            ->whereDate('created_at', now()->format('Y-m-d'))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('examination.index', compact('examinations'));
    }

    public function create($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $medicines = Medicine::where('stock', '>', 0)->orderBy('name')->get();
        $serviceActions = ServiceAction::where('is_active', true)->orderBy('name')->get();

        return view('examination.create', compact('patient', 'medicines', 'serviceActions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'complaint' => 'required|string',
            'blood_pressure' => 'nullable|string|max:20',
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'temperature' => 'nullable|numeric',
            'pulse' => 'nullable|integer',
            'diagnosis' => 'required|string',
            'actions' => 'nullable|array',
            'actions.*' => 'exists:service_actions,id',
            'notes' => 'nullable|string',
            'medicines' => 'nullable|array',
            'medicines.*.id' => 'required|exists:medicines,id',
            'medicines.*.qty' => 'required|integer|min:1',
            'medicines.*.instruction' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Get action names and calculate fees
            $actionNames = [];
            $actionFee = 0;
            if (isset($validated['actions'])) {
                $selectedActions = ServiceAction::whereIn('id', $validated['actions'])->get();
                foreach ($selectedActions as $action) {
                    $actionNames[] = $action->name;
                    $actionFee += $action->price;
                }
            }

            // Create examination
            $examination = Examination::create([
                'patient_id' => $validated['patient_id'],
                'doctor_id' => Auth::id(),
                'complaint' => $validated['complaint'],
                'blood_pressure' => $validated['blood_pressure'] ?? null,
                'weight' => $validated['weight'] ?? null,
                'height' => $validated['height'] ?? null,
                'temperature' => $validated['temperature'] ?? null,
                'pulse' => $validated['pulse'] ?? null,
                'diagnosis' => $validated['diagnosis'],
                'actions' => !empty($actionNames) ? implode(', ', $actionNames) : null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'selesai',
            ]);

            // Create prescriptions
            if (isset($validated['medicines'])) {
                foreach ($validated['medicines'] as $med) {
                    \App\Models\Prescription::create([
                        'examination_id' => $examination->id,
                        'medicine_id' => $med['id'],
                        'qty' => $med['qty'],
                        'instruction' => $med['instruction'] ?? null,
                        'status' => 'menunggu',
                    ]);
                }
            }

            // Create doctor payment
            DoctorPayment::create([
                'examination_id' => $examination->id,
                'patient_id' => $validated['patient_id'],
                'consultation_fee' => 50000,
                'action_fee' => $actionFee,
                'total' => 50000 + $actionFee,
                'status' => 'menunggu',
            ]);

            // Update queue status
            Queue::where('patient_id', $validated['patient_id'])
                ->whereDate('date', now()->format('Y-m-d'))
                ->where('status', 'dipanggil')
                ->update(['status' => 'selesai']);

            DB::commit();

            return redirect()->route('examination.index')
                ->with('success', 'Pemeriksaan berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan pemeriksaan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $examination = Examination::with(['patient', 'doctor', 'prescriptions.medicine'])->findOrFail($id);
        return view('examination.show', compact('examination'));
    }
}