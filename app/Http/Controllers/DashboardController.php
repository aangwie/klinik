<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Queue;
use App\Models\Examination;
use App\Models\DoctorPayment;
use App\Models\PharmacySale;
use App\Models\Prescription;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->format('Y-m-d');

        $totalPatientsToday = Queue::whereDate('created_at', $today)->count();
        $newPatientsToday = Patient::whereDate('created_at', $today)->count();
        $queueWaiting = Queue::whereDate('created_at', $today)->where('status', 'menunggu')->count();

        $todayDoctorRevenue = DoctorPayment::whereDate('created_at', $today)->where('status', 'lunas')->sum('total');
        $todayPharmacyRevenue = PharmacySale::whereDate('created_at', $today)->where('status', 'lunas')->sum('total');
        $todayRevenue = $todayDoctorRevenue + $todayPharmacyRevenue;

        $totalPatients = Patient::count();

        $recentVisits = Examination::with(['patient', 'doctor'])
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $topMedicines = Prescription::select('medicine_id', DB::raw('SUM(qty) as total_sold'))
            ->with('medicine')
            ->groupBy('medicine_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalPatientsToday',
            'newPatientsToday',
            'todayRevenue',
            'queueWaiting',
            'totalPatients',
            'recentVisits',
            'topMedicines'
        ));
    }
}