<?php

namespace App\Http\Controllers;

use App\Models\Examination;
use App\Models\DoctorPayment;
use App\Models\PharmacySale;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'harian');

        $data = [];

        if ($period == 'harian') {
            $today = now()->format('Y-m-d');

            $data['dailyVisits'] = Examination::with('patient')
                ->whereDate('created_at', $today)
                ->orderBy('created_at', 'desc')
                ->get();

            $data['dailyDoctorRevenue'] = DoctorPayment::whereDate('created_at', $today)
                ->where('status', 'lunas')
                ->sum('total');

            $data['dailyPharmacyRevenue'] = PharmacySale::whereDate('created_at', $today)
                ->where('status', 'lunas')
                ->sum('total');
        } elseif ($period == 'bulanan') {
            $startOfMonth = now()->startOfMonth()->format('Y-m-d');
            $endOfMonth = now()->endOfMonth()->format('Y-m-d');

            $data['monthlyDoctorRevenue'] = DoctorPayment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where('status', 'lunas')
                ->sum('total');

            $data['monthlyPharmacyRevenue'] = PharmacySale::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where('status', 'lunas')
                ->sum('total');

            $data['monthlyDoctorDetails'] = DoctorPayment::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total')
            )
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where('status', 'lunas')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $data['monthlyMedicineSales'] = Prescription::select(
                'medicines.name as medicine_name',
                DB::raw('SUM(prescriptions.qty) as qty'),
                DB::raw('SUM(prescriptions.qty * medicines.selling_price) as total')
            )
                ->join('medicines', 'prescriptions.medicine_id', '=', 'medicines.id')
                ->whereBetween('prescriptions.created_at', [$startOfMonth, $endOfMonth])
                ->where('prescriptions.status', 'selesai')
                ->groupBy('medicines.name')
                ->orderByDesc('qty')
                ->get();
        } else {
            $startOfYear = now()->startOfYear()->format('Y-m-d');
            $endOfYear = now()->endOfYear()->format('Y-m-d');

            $data['yearlyRevenue'] = DoctorPayment::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total) as total')
            )
                ->whereYear('created_at', now()->year)
                ->where('status', 'lunas')
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->orderBy(DB::raw('MONTH(created_at)'))
                ->get()
                ->map(function ($item) {
                    $item->month_name = \Carbon\Carbon::create()->month($item->month)->format('F');
                    return $item;
                });

            $data['yearlyTotalRevenue'] = DoctorPayment::whereYear('created_at', now()->year)
                ->where('status', 'lunas')
                ->sum('total') + PharmacySale::whereYear('created_at', now()->year)
                ->where('status', 'lunas')
                ->sum('total');

            $data['yearlyTotalVisits'] = Examination::whereYear('created_at', now()->year)->count();

            $data['totalPatients'] = Patient::count();
        }

        return view('report.index', $data);
    }
}