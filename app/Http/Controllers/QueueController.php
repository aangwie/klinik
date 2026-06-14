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

    public function cancel($id)
    {
        $queue = Queue::findOrFail($id);

        if (!in_array($queue->status, ['menunggu', 'dipanggil'])) {
            return redirect()->route('queue.index')
                ->with('error', 'Antrean dengan status "' . $queue->status . '" tidak dapat dibatalkan');
        }

        $patientName = $queue->patient->name;
        $queueNumber = $queue->queue_number;

        $queue->update(['status' => 'batal']);

        return redirect()->route('queue.index')
            ->with('success', "Antrean {$queueNumber} atas nama {$patientName} berhasil dibatalkan");
    }
}