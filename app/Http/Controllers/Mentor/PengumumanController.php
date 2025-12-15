<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PengumumanController extends Controller
{
    /**
     * Get pengumuman list untuk room tertentu (mentor view)
     */
    public function index($room_id)
    {
        $room = Room::where('room_id', $room_id)->firstOrFail();
        
        // Pastikan yang akses adalah mentor dari room ini
        if ($room->mentor_id !== Auth::user()->mentor->mentor_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $pengumumanList = Pengumuman::where('room_id', $room_id)
            ->with('mentor.user')
            ->orderByDesc('is_penting')
            ->latest()
            ->get()
            ->map(function($item) {
                return [
                    'pengumuman_id' => $item->pengumuman_id,
                    'judul' => $item->judul,
                    'isi' => $item->isi,
                    'is_penting' => $item->is_penting,
                    'durasi_tampil' => $item->durasi_tampil,
                    'durasi_text' => $item->durasi_tampil_text,
                    'tanggal_kadaluarsa' => $item->tanggal_kadaluarsa->format('d M Y H:i'),
                    'is_aktif' => $item->is_aktif,
                    'created_at' => $item->created_at->format('d M Y H:i')
                ];
            });
        
        return response()->json($pengumumanList);
    }
    
    /**
     * Store pengumuman baru
     */
    public function store(Request $request, $room_id)
    {
        try {
            $room = Room::where('room_id', $room_id)->firstOrFail();
            
            // Cek apakah user memiliki relasi mentor
            if (!Auth::user()->mentor) {
                return response()->json([
                    'error' => 'User tidak memiliki data mentor'
                ], 403);
            }
            
            if ($room->mentor_id !== Auth::user()->mentor->mentor_id) {
                return response()->json([
                    'error' => 'Unauthorized'
                ], 403);
            }
            
            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'isi' => 'required|string|max:500',
                'durasi_tampil' => 'required|in:24,72,168,720',
                'is_penting' => 'nullable|boolean'
            ]);
            
            // ✅ PERBAIKAN: Cast durasi_tampil ke integer
            $pengumuman = Pengumuman::create([
                'room_id' => $room_id,
                'mentor_id' => Auth::user()->mentor->mentor_id,
                'judul' => $validated['judul'],
                'isi' => $validated['isi'],
                'durasi_tampil' => (int) $validated['durasi_tampil'],
                'is_penting' => $validated['is_penting'] ?? false,
                'tanggal_kadaluarsa' => now()->addHours((int) $validated['durasi_tampil'])
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Pengumuman berhasil ditambahkan',
                'data' => [
                    'pengumuman_id' => $pengumuman->pengumuman_id,
                    'judul' => $pengumuman->judul,
                    'isi' => $pengumuman->isi,
                    'is_penting' => $pengumuman->is_penting,
                    'durasi_text' => $pengumuman->durasi_tampil_text,
                    'tanggal_kadaluarsa' => $pengumuman->tanggal_kadaluarsa->format('d M Y H:i'),
                    'created_at' => $pengumuman->created_at->format('d M Y H:i')
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Error creating pengumuman: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'error' => 'Terjadi kesalahan saat membuat pengumuman',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update pengumuman
     */
    public function update(Request $request, $room_id, $pengumuman_id)
    {
        try {
            $room = Room::where('room_id', $room_id)->firstOrFail();
            
            if ($room->mentor_id !== Auth::user()->mentor->mentor_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $pengumuman = Pengumuman::where('pengumuman_id', $pengumuman_id)
                ->where('room_id', $room_id)
                ->firstOrFail();
            
            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'isi' => 'required|string|max:500',
                'durasi_tampil' => 'required|in:24,72,168,720',
                'is_penting' => 'nullable|boolean'
            ]);
            
            // ✅ PERBAIKAN: Cast ke integer
            $pengumuman->judul = $validated['judul'];
            $pengumuman->isi = $validated['isi'];
            $pengumuman->durasi_tampil = (int) $validated['durasi_tampil'];
            $pengumuman->is_penting = $validated['is_penting'] ?? false;
            $pengumuman->tanggal_kadaluarsa = now()->addHours((int) $validated['durasi_tampil']);
            $pengumuman->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Pengumuman berhasil diupdate',
                'data' => [
                    'pengumuman_id' => $pengumuman->pengumuman_id,
                    'judul' => $pengumuman->judul,
                    'isi' => $pengumuman->isi,
                    'is_penting' => $pengumuman->is_penting,
                    'durasi_text' => $pengumuman->durasi_tampil_text,
                    'tanggal_kadaluarsa' => $pengumuman->tanggal_kadaluarsa->format('d M Y H:i'),
                    'created_at' => $pengumuman->created_at->format('d M Y H:i')
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating pengumuman: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Terjadi kesalahan saat update pengumuman',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete pengumuman
     */
    public function destroy($room_id, $pengumuman_id)
    {
        try {
            $room = Room::where('room_id', $room_id)->firstOrFail();
            
            if ($room->mentor_id !== Auth::user()->mentor->mentor_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $pengumuman = Pengumuman::where('pengumuman_id', $pengumuman_id)
                ->where('room_id', $room_id)
                ->firstOrFail();
            
            $pengumuman->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Pengumuman berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting pengumuman: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Terjadi kesalahan saat hapus pengumuman',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}