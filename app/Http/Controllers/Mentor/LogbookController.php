<?php

namespace App\Http\Controllers\Mentor;

use App\Models\Room;
use App\Models\Logbook;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class LogbookController extends Controller
{
    /**
     * Display logbook peserta di room yang di-handle mentor
     */
    public function index(Request $request)
    {
        // Ambil room yang di-handle mentor ini
        $rooms = Room::where('mentor_id', auth()->user()->mentor->mentor_id)->get();
        $roomIds = $rooms->pluck('room_id');

        // Query logbook
        $query = Logbook::whereIn('room_id', $roomIds)
            ->with(['user', 'room']);

        // Filter by room (optional)
        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        // Filter by status (optional)
        if ($request->filled('status')) {
            if ($request->status == 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status == 'pending') {
                $query->where('is_approved', false);
            }
        }

        $logbooks = $query->orderBy('date', 'desc')->paginate(20);

        return view('mentor.logbook.index', compact('logbooks', 'rooms'));
    }

    /**
     * Toggle approval logbook
     */
    public function toggleApproval($id)
    {
        // Ambil room yang di-handle mentor ini
        $roomIds = Room::where('mentor_id', auth()->user()->mentor->mentor_id)
            ->pluck('room_id');

        $logbook = Logbook::whereIn('room_id', $roomIds)->findOrFail($id);

        if ($logbook->is_approved) {
            // Unapprove
            $logbook->update([
                'is_approved' => false,
                'approved_by' => null,
                'approved_at' => null,
            ]);
            $message = 'Logbook berhasil di-unapprove.';
        } else {
            // Approve
            $logbook->update([
                'is_approved' => true,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            $message = 'Logbook berhasil di-approve!';
        }

        return back()->with('success', $message);
    }

    /**
     * Update keterangan logbook
     */
    public function updateKeterangan(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|in:offline_kantor,sakit,izin,online,alpha',
        ]);

        // Ambil room yang di-handle mentor ini
        $roomIds = Room::where('mentor_id', auth()->user()->mentor->mentor_id)
            ->pluck('room_id');

        $logbook = Logbook::whereIn('room_id', $roomIds)->findOrFail($id);

        $logbook->update([
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Keterangan berhasil diupdate!');
    }
}