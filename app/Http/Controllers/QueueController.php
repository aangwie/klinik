<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function index()
    {
        $queues = Queue::with('patient')
            ->whereDate('date', now()->format('Y-m-d'))
            ->orderBy('created_at', 'asc')
            ->get();

        return view('queue.index', compact('queues'));
    }

    public function call($id)
    {
        $queue = Queue::findOrFail($id);
        $queue->update(['status' => 'dipanggil']);

        return redirect()->route('queue.index')
            ->with('success', "Pasien {$queue->patient->name} dipanggil");
    }
}