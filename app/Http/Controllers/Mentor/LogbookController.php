<?php

namespace App\Http\Controllers\Mentor;

use App\Models\Room;
use App\Models\Logbook;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // Tentukan mode: active atau archive
        $mode = $request->get('mode', 'active'); // default: active

        // Query logbook
        $query = Logbook::whereIn('room_id', $roomIds)
            ->with(['user.peserta', 'user', 'room']);

        // Filter berdasarkan status peserta (active/finished)
        if ($mode === 'archive') {
            // Logbook dari peserta yang sudah selesai magang (periode_end sudah lewat)
            $query->whereHas('user.peserta', function($q) {
                $q->where('periode_end', '<', now());
            });
        } else {
            // Logbook dari peserta yang masih aktif (periode_end >= hari ini atau null)
            $query->whereHas('user.peserta', function($q) {
                $q->where(function($subQuery) {
                    $subQuery->where('periode_end', '>=', now())
                             ->orWhereNull('periode_end');
                });
            });
        }

        // Filter by room (optional)
        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        // Filter by status approval (optional)
        if ($request->filled('status')) {
            if ($request->status == 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status == 'pending') {
                $query->where('is_approved', false);
            }
        }

        $logbooks = $query->orderBy('date', 'desc')->paginate(20);

        // Hitung jumlah untuk badge
        $activeCount = Logbook::whereIn('room_id', $roomIds)
            ->whereHas('user.peserta', function($q) {
                $q->where(function($subQuery) {
                    $subQuery->where('periode_end', '>=', now())
                             ->orWhereNull('periode_end');
                });
            })
            ->count();

        $archiveCount = Logbook::whereIn('room_id', $roomIds)
            ->whereHas('user.peserta', function($q) {
                $q->where('periode_end', '<', now());
            })
            ->count();

        return view('mentor.logbook.index', compact('logbooks', 'rooms', 'mode', 'activeCount', 'archiveCount'));
    }

    /**
     * Approve logbook (sesuai keterangan yang diisi peserta) - AJAX
     */
    public function approve(Request $request, $id)
    {
        // Ambil room yang di-handle mentor ini
        $roomIds = Room::where('mentor_id', auth()->user()->mentor->mentor_id)
            ->pluck('room_id');

        $logbook = Logbook::whereIn('room_id', $roomIds)->findOrFail($id);

        // Approve dengan keterangan yang sudah diisi peserta
        $logbook->update([
            'is_approved' => true,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'is_approved' => true
            ]);
        }

        return back()->with('success', 'Logbook berhasil di-approve!');
    }

    /**
     * Approve logbook dengan mengubah keterangan - AJAX
     */
    public function approveWithKeterangan(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|in:offline_kantor,sakit,izin,alpha',
        ]);

        // Ambil room yang di-handle mentor ini
        $roomIds = Room::where('mentor_id', auth()->user()->mentor->mentor_id)
            ->pluck('room_id');

        $logbook = Logbook::whereIn('room_id', $roomIds)->findOrFail($id);

        // Simpan keterangan asli peserta jika belum ada
        if (!$logbook->keterangan_asli) {
            $logbook->keterangan_asli = $logbook->keterangan;
        }

        // Approve + ubah keterangan
        $logbook->update([
            'keterangan' => $request->keterangan,
            'is_approved' => true,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $keteranganLabel = [
            'offline_kantor' => 'Offline Kantor',
            'sakit' => 'Sakit',
            'izin' => 'Izin',
            'alpha' => 'Alpha',
        ];

        $keteranganColor = [
            'offline_kantor' => '#0d6efd',
            'online' => '#0dcaf0',
            'sakit' => '#ffc107',
            'izin' => '#6c757d',
            'alpha' => '#dc3545',
        ];

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'is_approved' => true,
                'keterangan' => $request->keterangan,
                'keterangan_label' => $keteranganLabel[$request->keterangan],
                'keterangan_color' => $keteranganColor[$request->keterangan]
            ]);
        }

        return back()->with('success', 'Logbook berhasil di-approve dengan keterangan ' . $keteranganLabel[$request->keterangan] . '!');
    }

    /**
     * Unapprove logbook (batalkan approval) - AJAX
     */
    public function unapprove(Request $request, $id)
    {
        // Ambil room yang di-handle mentor ini
        $roomIds = Room::where('mentor_id', auth()->user()->mentor->mentor_id)
            ->pluck('room_id');

        $logbook = Logbook::whereIn('room_id', $roomIds)->findOrFail($id);

        // Kembalikan ke keterangan asli jika ada
        $keteranganKembali = $logbook->keterangan_asli ?? $logbook->keterangan;

        // Unapprove
        $logbook->update([
            'keterangan' => $keteranganKembali,
            'is_approved' => false,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'is_approved' => false
            ]);
        }

        return back()->with('success', 'Logbook berhasil dibatalkan (unapproved).');
    }

    public function showPeserta($user_id)
    {
        // Ambil data peserta dengan relasi yang diperlukan
        $peserta = \App\Models\User::where('id', $user_id)
            ->where('role', 'peserta')
            ->with(['peserta', 'joinedRooms'])
            ->firstOrFail();
        
        // Optional: Validasi bahwa mentor hanya bisa lihat peserta di room mereka
        $mentorRoomIds = Auth::user()->mentor->rooms->pluck('room_id');
        $pesertaRoomIds = $peserta->joinedRooms->pluck('room_id');
        
        // Cek apakah ada irisan antara room mentor dan room peserta
        if ($mentorRoomIds->intersect($pesertaRoomIds)->isEmpty()) {
            abort(403, 'Anda tidak memiliki akses ke peserta ini');
        }
        
        return view('mentor.logbook.peserta', compact('peserta'));
    }
}