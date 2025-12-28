<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use App\Models\Room;
use App\Models\Peserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $peserta = Peserta::where('peserta_id', $user->id)->first();

        if (!$peserta) {
            abort(403, 'Data peserta tidak ditemukan.');
        }

        // Materi dari room "general"
        $generalMaterials = Materi::whereHas('room', function ($query) {
                $query->whereRaw('LOWER(nama_room) = ?', ['general']);
            })
            ->with('room')
            ->orderBy('created_at', 'desc')
            ->get();

        // Room yang diikuti peserta (bukan "general")
        $joinedRooms = Room::whereHas('peserta', function ($query) use ($peserta) {
                $query->where('users.id', $peserta->peserta_id);
            })
            ->whereRaw('LOWER(nama_room) != ?', ['general'])
            ->with(['materis' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->get();

        return view('peserta.materials.index', compact('generalMaterials', 'joinedRooms'));
    }

    public function view($id, Request $request)
    {
        $user = Auth::user();
        $peserta = Peserta::where('peserta_id', $user->id)->first();
        
        // Tambahkan 'room.materis' untuk load materi lain dari room yang sama
        $materi = Materi::with(['room.materis'])->findOrFail($id);

        if (strtolower($materi->room->nama_room) !== 'general') {
            $hasAccess = $materi->room->peserta()
                ->where('users.id', $peserta->peserta_id)
                ->exists();

            if (!$hasAccess) {
                abort(403, 'Anda tidak memiliki akses ke materi ini. Silakan join room terlebih dahulu.');
            }
        }

        // Ambil info dari query parameter
        $fromRoom = $request->query('from') === 'room';
        $backUrl = $request->query('back', route('peserta.materials'));

        return view('peserta.materials.view', compact('materi', 'fromRoom', 'backUrl'));
    }

    public function download($id)
    {
        $user = Auth::user();
        $peserta = Peserta::where('peserta_id', $user->id)->first();
        $materi = Materi::with('room')->findOrFail($id);

        // Cek akses
        if (strtolower($materi->room->nama_room) !== 'general') {
            $hasAccess = $materi->room->peserta()
                ->where('users.id', $peserta->peserta_id)
                ->exists();

            if (!$hasAccess) {
                abort(403, 'Anda tidak memiliki akses ke materi ini.');
            }
        }

        // Cek apakah ada file_path (file lampiran)
        if (!$materi->file_path) {
            return redirect()->back()->with('error', 'Materi ini tidak memiliki file lampiran.');
        }

        // Cek file exist
        if (!Storage::disk('public')->exists($materi->file_path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di server.');
        }

        // Download file
        return Storage::disk('public')->download($materi->file_path);
    }

    public function viewPdf($id)
    {
        $user = Auth::user();
        $peserta = Peserta::where('peserta_id', $user->id)->first();
        $materi = Materi::with('room')->findOrFail($id);

        // Cek akses
        if (strtolower($materi->room->nama_room) !== 'general') {
            $hasAccess = $materi->room->peserta()
                ->where('users.id', $peserta->peserta_id)
                ->exists();

            if (!$hasAccess) {
                abort(403, 'Anda tidak memiliki akses ke materi ini.');
            }
        }

        // Cek file_path
        if (!$materi->file_path) {
            abort(404, 'File PDF tidak tersedia.');
        }

        $filePath = 'public/' . $materi->file_path;
        if (!Storage::exists($filePath)) {
            abort(404, 'File PDF tidak ditemukan.');
        }

        return response()->file(Storage::path($filePath));
    }

    public function stream($id)
    {
        $user = Auth::user();
        $peserta = Peserta::where('peserta_id', $user->id)->first();
        $materi = Materi::with('room')->findOrFail($id);

        // Cek akses
        if (strtolower($materi->room->nama_room) !== 'general') {
            $hasAccess = $materi->room->peserta()
                ->where('users.id', $peserta->peserta_id)
                ->exists();

            if (!$hasAccess) {
                abort(403, 'Anda tidak memiliki akses ke materi ini.');
            }
        }

        // Cek file_path
        if (!$materi->file_path) {
            abort(404, 'File video tidak tersedia.');
        }

        $filePath = 'public/' . $materi->file_path;
        if (!Storage::exists($filePath)) {
            abort(404, 'File video tidak ditemukan.');
        }

        return response()->file(Storage::path($filePath), [
            'Content-Type' => 'video/mp4',
        ]);
    }
}