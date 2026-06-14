<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index()
    {
        $medicines = Medicine::orderBy('name')->get();
        return view('medicine.index', compact('medicines'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:medicines,code',
            'name' => 'required|string|max:100',
            'category' => 'nullable|string|max:50',
            'unit' => 'nullable|string|max:20',
            'stock' => 'nullable|integer|min:0',
            'purchase_price' => 'nullable|integer|min:0',
            'selling_price' => 'required|integer|min:0',
            'expired_date' => 'nullable|date',
        ]);

        Medicine::create($validated);

        return redirect()->route('medicine.index')
            ->with('success', 'Obat berhasil ditambahkan');
    }

    public function edit($id)
    {
        $medicine = Medicine::findOrFail($id);
        return response()->json($medicine);
    }

    public function update(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:medicines,code,' . $id,
            'name' => 'required|string|max:100',
            'category' => 'nullable|string|max:50',
            'unit' => 'nullable|string|max:20',
            'stock' => 'nullable|integer|min:0',
            'purchase_price' => 'nullable|integer|min:0',
            'selling_price' => 'required|integer|min:0',
            'expired_date' => 'nullable|date',
        ]);

        $medicine->update($validated);

        return redirect()->route('medicine.index')
            ->with('success', 'Obat berhasil diperbarui');
    }

    public function destroy($id)
    {
        $medicine = Medicine::findOrFail($id);
        $medicine->delete();

        return redirect()->route('medicine.index')
            ->with('success', 'Obat berhasil dihapus');
    }

    public function export()
    {
        // Simple CSV export
        $medicines = Medicine::orderBy('name')->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="daftar-obat-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($medicines) {
            $file = fopen('php://output', 'w');
            fprintf($file, "\xEF\xBB\xBF"); // BOM for UTF-8
            fputcsv($file, ['Kode', 'Nama Obat', 'Kategori', 'Satuan', 'Stok', 'Harga Beli', 'Harga Jual', 'Expired']);

            foreach ($medicines as $m) {
                fputcsv($file, [
                    $m->code,
                    $m->name,
                    $m->category,
                    $m->unit,
                    $m->stock,
                    $m->purchase_price,
                    $m->selling_price,
                    $m->expired_date ? $m->expired_date->format('d/m/Y') : '',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}