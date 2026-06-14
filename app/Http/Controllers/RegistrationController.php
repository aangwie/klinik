<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Queue;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function index()
    {
        return view('registration.index');
    }

    public function store(Request $request)
    {
        if ($request->type == 'new') {
            $validated = $request->validate([
                'nik' => 'required|string|size:16|unique:patients,nik',
                'name' => 'required|string|max:100',
                'birth_date' => 'required|date',
                'gender' => 'required|in:L,P',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'occupation' => 'nullable|string|max:100',
            ]);

            // Generate medical record number
            $year = now()->format('Y');
            $lastPatient = Patient::whereYear('created_at', $year)->latest()->first();
            $lastNumber = $lastPatient ? intval(substr($lastPatient->medical_record_number, -6)) : 0;
            $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
            $validated['medical_record_number'] = "RM{$year}{$newNumber}";

            $patient = Patient::create($validated);
        } else {
            $request->validate([
                'patient_id' => 'required|exists:patients,id',
            ]);
            $patient = Patient::findOrFail($request->patient_id);
        }

        // Generate queue number
        $today = now()->format('Y-m-d');
        $lastQueue = Queue::whereDate('date', $today)->latest()->first();
        $lastQueueNum = $lastQueue ? intval(substr($lastQueue->queue_number, 1)) : 0;
        $newQueueNum = str_pad($lastQueueNum + 1, 3, '0', STR_PAD_LEFT);
        $queueNumber = 'A' . $newQueueNum;

        Queue::create([
            'queue_number' => $queueNumber,
            'patient_id' => $patient->id,
            'status' => 'menunggu',
            'date' => $today,
        ]);

        $redirectRoute = auth()->user()->role == 'pendaftaran' ? 'registration.index' : 'queue.index';

        return redirect()->route($redirectRoute)
            ->with('success', "Pasien {$patient->name} berhasil didaftarkan. No. Antrean: {$queueNumber}");
    }

    public function searchPatients(Request $request)
    {
        $query = $request->get('q');
        $patients = Patient::where('name', 'like', "%{$query}%")
            ->orWhere('nik', 'like', "%{$query}%")
            ->orWhere('medical_record_number', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json($patients);
    }
}