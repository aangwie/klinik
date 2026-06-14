<?php

namespace App\Http\Controllers;

use App\Models\ServiceAction;
use Illuminate\Http\Request;

class ServiceActionController extends Controller
{
    public function index()
    {
        $serviceActions = ServiceAction::orderBy('name')->get();
        return view('service-action.index', compact('serviceActions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:service_actions,name',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
        ]);

        ServiceAction::create($validated);

        return redirect()->route('service-action.index')
            ->with('success', 'Jasa/Tindakan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $action = ServiceAction::findOrFail($id);
        return response()->json($action);
    }

    public function update(Request $request, $id)
    {
        $action = ServiceAction::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:service_actions,name,' . $id,
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
        ]);

        $action->update($validated);

        return redirect()->route('service-action.index')
            ->with('success', 'Jasa/Tindakan berhasil diperbarui');
    }

    public function toggleActive($id)
    {
        $action = ServiceAction::findOrFail($id);
        $action->update(['is_active' => !$action->is_active]);

        return redirect()->route('service-action.index')
            ->with('success', 'Status jasa/tindakan berhasil diubah');
    }

    public function destroy($id)
    {
        $action = ServiceAction::findOrFail($id);
        $action->delete();

        return redirect()->route('service-action.index')
            ->with('success', 'Jasa/Tindakan berhasil dihapus');
    }
}