<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Examination;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q');

        $patients = Patient::withCount('examinations')
            ->with(['examinations' => function($q) {
                $q->latest()->take(1);
            }]);

        if ($query) {
            $patients->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('nik', 'like', "%{$query}%")
                  ->orWhere('medical_record_number', 'like', "%{$query}%");
            });
        }

        $patients = $patients->orderBy('name')->paginate(20);

        // Add last examination date
        foreach ($patients as $patient) {
            $patient->last_examination = $patient->examinations->first()?->created_at;
        }

        return view('medical-record.index', compact('patients'));
    }

    public function show($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $examinations = Examination::with(['doctor', 'prescriptions.medicine'])
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('medical-record.show', compact('patient', 'examinations'));
    }

    public function pdf($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $examinations = Examination::with(['doctor', 'prescriptions.medicine'])
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();

        // For now, return view that can be printed as PDF
        return view('medical-record.pdf', compact('patient', 'examinations'));
    }
}