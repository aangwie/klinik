<?php

namespace App\Http\Controllers;

use App\Models\Examination;
use App\Models\DoctorPayment;
use App\Models\PharmacySale;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Medicine;
use App\Models\ServiceAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'harian');
        $data = [];

        // Date range filter
        $startDate = $request->get('start_date', now()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $month = $request->get('month', now()->format('Y-m'));

        if ($period == 'harian') {
            // Filter by date range: start_date to end_date
            $startDateTime = $startDate . ' 00:00:00';
            $endDateTime = $endDate . ' 23:59:59';

            $data['dailyVisits'] = Examination::with('patient')
                ->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->orderBy('created_at', 'desc')
                ->get();

            $data['dailyDoctorRevenue'] = DoctorPayment::whereBetween('created_at', [$startDateTime, $endDateTime])
                ->where('status', 'lunas')
                ->sum('total');

            $data['dailyPharmacyRevenue'] = PharmacySale::whereBetween('created_at', [$startDateTime, $endDateTime])
                ->where('status', 'lunas')
                ->sum('total');
        } elseif ($period == 'bulanan') {
            $startOfMonth = \Carbon\Carbon::parse($month . '-01')->startOfMonth()->format('Y-m-d');
            $endOfMonth = \Carbon\Carbon::parse($month . '-01')->endOfMonth()->format('Y-m-d');

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
                    $item->month_name = \Carbon\Carbon::create()->month($item->month)->isoFormat('MMMM');
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

        // ANALISIS - Action analysis (seluruh waktu)
        $data['actionAnalysis'] = Examination::select('actions', DB::raw('COUNT(*) as total'))
            ->whereNotNull('actions')
            ->where('actions', '!=', '')
            ->groupBy('actions')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // ANALISIS - Top medicines (seluruh waktu)
        $data['medicineAnalysis'] = Prescription::select(
            'medicines.name as medicine_name',
            'medicines.selling_price',
            DB::raw('SUM(prescriptions.qty) as total_qty'),
            DB::raw('SUM(prescriptions.qty * medicines.selling_price) as total_value')
        )
            ->join('medicines', 'prescriptions.medicine_id', '=', 'medicines.id')
            ->where('prescriptions.status', 'selesai')
            ->groupBy('medicines.name', 'medicines.selling_price')
            ->orderByDesc('total_qty')
            ->take(10)
            ->get();

        // ANALISIS - Low stock medicines (stok <= low_stock)
        $data['lowStockMedicines'] = Medicine::lowStock()->orderBy('stock')->get();

        // ANALISIS - Expiring medicines (6 bulan ke depan)
        $data['expiringMedicines'] = Medicine::expiringSoon(6)->orderBy('expired_date')->get();

        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        $data['selectedMonth'] = $month;

        return view('report.index', $data);
    }
}