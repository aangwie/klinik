<?php

namespace App\Http\Controllers;

use App\Models\DoctorProfile;
use App\Models\User;
use Illuminate\Http\Request;

class DoctorProfileController extends Controller
{
    public function index()
    {
        $profiles = DoctorProfile::with('user')->orderBy('full_name')->get();
        $doctors = User::where('role', 'dokter')->whereDoesntHave('doctorProfile')->get();
        return view('doctor-profile.index', compact('profiles', 'doctors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:doctor_profiles,user_id',
            'full_name' => 'required|string|max:100',
            'address' => 'nullable|string',
            'birth_place' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'str_number' => 'nullable|string|max:50|unique:doctor_profiles,str_number',
            'specialization' => 'nullable|string|max:100',
            'consultation_fee' => 'required|integer|min:0',
        ]);

        DoctorProfile::create($validated);

        return redirect()->route('doctor-profile.index')
            ->with('success', 'Profil dokter berhasil ditambahkan');
    }

    public function edit($id)
    {
        $profile = DoctorProfile::findOrFail($id);
        return response()->json($profile);
    }

    public function update(Request $request, $id)
    {
        $profile = DoctorProfile::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:doctor_profiles,user_id,' . $id,
            'full_name' => 'required|string|max:100',
            'address' => 'nullable|string',
            'birth_place' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'str_number' => 'nullable|string|max:50|unique:doctor_profiles,str_number,' . $id,
            'specialization' => 'nullable|string|max:100',
            'consultation_fee' => 'required|integer|min:0',
        ]);

        $profile->update($validated);

        return redirect()->route('doctor-profile.index')
            ->with('success', 'Profil dokter berhasil diperbarui');
    }

    public function toggleAvailability($id)
    {
        $profile = DoctorProfile::findOrFail($id);
        $profile->update(['is_available' => !$profile->is_available]);

        $status = $profile->is_available ? 'tersedia' : 'tidak tersedia';
        return redirect()->route('doctor-profile.index')
            ->with('success', "Status {$profile->full_name} sekarang {$status}");
    }

    public function destroy($id)
    {
        $profile = DoctorProfile::findOrFail($id);
        $profile->delete();

        return redirect()->route('doctor-profile.index')
            ->with('success', 'Profil dokter berhasil dihapus');
    }
}
