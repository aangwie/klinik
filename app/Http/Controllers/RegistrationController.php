<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Queue;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function index()
    {
        $doctors = DoctorProfile::with('user')->available()->orderBy('full_name')->get();
        return view('registration.index', compact('doctors'));
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

        $request->validate([
            'doctor_profile_id' => 'required|exists:doctor_profiles,id',
        ]);

        $doctorProfile = DoctorProfile::findOrFail($request->doctor_profile_id);

        // Get prefix from doctor's first name initial
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $doctorProfile->full_name), 0, 1));
        if (!preg_match('/[A-Z]/', $prefix)) $prefix = 'A';

        // Generate queue number with prefix
        $today = now()->format('Y-m-d');
        $lastQueue = Queue::whereDate('date', $today)
            ->where('queue_number', 'like', $prefix . '%')
            ->latest()
            ->first();
        $lastQueueNum = $lastQueue ? intval(substr($lastQueue->queue_number, 1)) : 0;
        $newQueueNum = str_pad($lastQueueNum + 1, 3, '0', STR_PAD_LEFT);
        $queueNumber = $prefix . $newQueueNum;

        Queue::create([
            'queue_number' => $queueNumber,
            'patient_id' => $patient->id,
            'doctor_profile_id' => $doctorProfile->id,
            'status' => 'menunggu',
            'date' => $today,
        ]);

        $redirectRoute = auth()->user()->role == 'pendaftaran' ? 'registration.index' : 'queue.index';

        return redirect()->route($redirectRoute)
            ->with('success', "Pasien {$patient->name} berhasil didaftarkan ke {$doctorProfile->full_name}. No. Antrean: {$queueNumber}");
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