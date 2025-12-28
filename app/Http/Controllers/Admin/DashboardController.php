<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Room;
use App\Models\Peserta;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data admin yang sedang login
        $admin = Auth::user();
        
        // Ambil semua room yang ada
         // Ambil semua room dengan relasi user mentor
         $rooms = Room::with(['mentor.user'])->get();
        
        // Ambil semua user dengan role peserta
        $allPeserta = User::where('role', 'peserta')
            ->with(['peserta', 'joinedRooms'])
            ->get();
        
        // Filter peserta aktif (periode belum berakhir)
        $pesertaAktif = $allPeserta->filter(function($user) {
            return $user->peserta && $user->peserta->periode_end >= Carbon::now();
        });
        
        // Filter peserta selesai (periode sudah berakhir)
        $pesertaSelesai = $allPeserta->filter(function($user) {
            return $user->peserta && $user->peserta->periode_end < Carbon::now();
        });
        
        // Total peserta aktif
        $totalPeserta = $pesertaAktif->count();
        
        // Data peserta dengan detail
        $pesertaData = $pesertaAktif->map(function($user) {
            $pesertaDetail = $user->peserta;
            $sisaHari = max(0, (int) Carbon::now()->diffInDays($pesertaDetail->periode_end, false));
            
            // Ambil room peserta (ambil yang pertama jika ada di banyak room)
            $userRoom = $user->joinedRooms->first();
            
            return [
                'nama' => $user->nama,
                'institut' => $pesertaDetail->institut,
                'periode' => Carbon::parse($pesertaDetail->periode_start)->format('F') . ' - ' . 
                           Carbon::parse($pesertaDetail->periode_end)->format('F'),
                'periode_start' => $pesertaDetail->periode_start,
                'periode_end' => $pesertaDetail->periode_end,
                'sisaHari' => max(0, $sisaHari),
                'status' => 'Aktif',
                'room_nama' => $userRoom ? $userRoom->nama_room : '-',
                'room_id' => $userRoom ? $userRoom->room_id : null
            ];
        });
        
        // Group by periode untuk countdown cards
        $periodeData = $pesertaData->groupBy('periode')->map(function($items, $periode) {
            $firstItem = $items->first();
            return [
                'periode' => $periode,
                'jumlah_peserta' => $items->count(),
                'sisaHari' => $firstItem['sisaHari'],
                'periode_end' => $firstItem['periode_end']
            ];
        })->values();
        
        // Data peserta aktif per institut
        $institutActive = $pesertaData->groupBy('institut')->map(function($items, $institut) {
            return [
                'institut' => $institut,
                'total' => $items->count()
            ];
        })->values();
        
        // Data peserta selesai per institut
        $pesertaSelesaiData = $pesertaSelesai->map(function($user) {
            $pesertaDetail = $user->peserta;
            return [
                'nama' => $user->nama,
                'institut' => $pesertaDetail->institut,
                'periode' => Carbon::parse($pesertaDetail->periode_start)->format('F Y') . ' - ' . 
                           Carbon::parse($pesertaDetail->periode_end)->format('F Y'),
                'selesai' => Carbon::parse($pesertaDetail->periode_end)->format('F Y'),
                'status' => 'Selesai'
            ];
        });
        
        $institutCompleted = $pesertaSelesaiData->groupBy('institut')->map(function($items, $institut) {
            return [
                'institut' => $institut,
                'total' => $items->count()
            ];
        })->values();
      
        // Data room dengan jumlah peserta
        // Data room dengan jumlah peserta
         $roomsData = $rooms->map(function($room) {
            $roomPesertaCount = $room->peserta()
               ->whereHas('peserta', function($q) {
                     $q->where('periode_end', '>=', now());
               })
               ->count();
            
            // Ambil user mentor dulu, baru ambil nama dari user
            $mentorNama = '-';
            if ($room->mentor_id) {
               $userMentor = User::find($room->mentor_id);
               if ($userMentor) {
                     $mentorNama = $userMentor->nama;
               }
            }
            
            return [
               'room_id' => $room->room_id,
               'nama_room' => $room->nama_room,
               'jumlah_peserta' => $roomPesertaCount,
               'mentor_nama' => $mentorNama
            ];
         });
               
        // Statistik singkat
        $stats = [
            'peserta_aktif' => $totalPeserta,
            'peserta_selesai' => $pesertaSelesai->count(),
            'periode_berjalan' => $periodeData->count(),
            'institut_berbeda' => $institutActive->count(),
            'total_rooms' => $rooms->count()
        ];
        
        return view('admin.dashboard', compact(
            'admin',
            'rooms',
            'roomsData',
            'totalPeserta',
            'pesertaData',
            'periodeData',
            'institutActive',
            'institutCompleted',
            'stats'
        ));
    }

    public function showPeserta($user_id)
    {
        // Ambil data peserta dengan relasi yang diperlukan
        $peserta = User::where('id', $user_id)
            ->where('role', 'peserta')
            ->with(['peserta', 'joinedRooms'])
            ->firstOrFail();
        
        return view('admin.home.peserta', compact('peserta'));
    }
    
    // API untuk modal detail peserta per periode
    public function getPeriodDetail($periode)
    {
        $data = User::where('role', 'peserta')
            ->with(['peserta', 'joinedRooms'])
            ->get()
            ->filter(function($user) use ($periode) {
                if (!$user->peserta) return false;
                
                $userPeriode = Carbon::parse($user->peserta->periode_start)->format('F') . ' - ' . 
                              Carbon::parse($user->peserta->periode_end)->format('F');
                
                return $userPeriode === $periode && 
                       $user->peserta->periode_end >= Carbon::now();
            })
            ->map(function($user) {
                $sisaHari = (int) Carbon::now()->diffInDays($user->peserta->periode_end, false);
                $userRoom = $user->joinedRooms->first();
                
                return [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'institut' => $user->peserta->institut,
                    'sisaHari' => max(0, $sisaHari),
                    'status' => 'Aktif',
                    'room' => $userRoom ? $userRoom->nama_room : '-'
                ];
            })
            ->values();
        
        return response()->json($data);
    }
    
    // API untuk modal detail peserta per institut
    public function getInstitutDetail($institut, $type)
    {
        $users = User::where('role', 'peserta')
            ->with(['peserta', 'joinedRooms'])
            ->get();
        
        if ($type === 'active') {
            $data = $users->filter(function($user) use ($institut) {
                return $user->peserta && 
                       $user->peserta->institut === $institut &&
                       $user->peserta->periode_end >= Carbon::now();
            })
            ->map(function($user) {
                $sisaHari = (int) Carbon::now()->diffInDays($user->peserta->periode_end, false);
                $userRoom = $user->joinedRooms->first();
                
                return [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'periode' => Carbon::parse($user->peserta->periode_start)->format('F') . ' - ' . 
                               Carbon::parse($user->peserta->periode_end)->format('F'),
                    'sisaHari' => max(0, $sisaHari),
                    'status' => 'Aktif',
                    'room' => $userRoom ? $userRoom->nama_room : '-'
                ];
            })
            ->values();
        } else {
            $data = $users->filter(function($user) use ($institut) {
                return $user->peserta && 
                       $user->peserta->institut === $institut &&
                       $user->peserta->periode_end < Carbon::now();
            })
            ->map(function($user) {
                $userRoom = $user->joinedRooms->first();
                
                return [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'periode' => Carbon::parse($user->peserta->periode_start)->format('F Y') . ' - ' . 
                               Carbon::parse($user->peserta->periode_end)->format('F Y'),
                    'selesai' => Carbon::parse($user->peserta->periode_end)->format('F Y'),
                    'status' => 'Selesai',
                    'room' => $userRoom ? $userRoom->nama_room : '-'
                ];
            })
            ->values();
        }
        
        return response()->json($data);
    }
    
    // API untuk modal detail semua peserta aktif
    public function getAllPesertaDetail()
    {
        $data = User::where('role', 'peserta')
            ->with(['peserta', 'joinedRooms'])
            ->get()
            ->filter(function($user) {
                return $user->peserta && $user->peserta->periode_end >= Carbon::now();
            })
            ->map(function($user) {
                $sisaHari = (int) Carbon::now()->diffInDays($user->peserta->periode_end, false);
                $userRoom = $user->joinedRooms->first();
                
                return [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'institut' => $user->peserta->institut,
                    'periode' => Carbon::parse($user->peserta->periode_start)->format('F') . ' - ' . 
                               Carbon::parse($user->peserta->periode_end)->format('F'),
                    'sisaHari' => max(0, $sisaHari),
                    'status' => 'Aktif',
                    'room' => $userRoom ? $userRoom->nama_room : '-'
                ];
            })
            ->values();
        
        return response()->json($data);
    }
    
    // API untuk detail per room
    public function getRoomDetailById($roomId)
    {
        $room = Room::findOrFail($roomId);
        
        $data = $room->peserta()
            ->with('peserta')
            ->get()
            ->filter(function($user) {
                return $user->peserta && $user->peserta->periode_end >= Carbon::now();
            })
            ->map(function($user) {
                $sisaHari = (int) Carbon::now()->diffInDays($user->peserta->periode_end, false);
                return [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'institut' => $user->peserta->institut,
                    'periode' => Carbon::parse($user->peserta->periode_start)->format('F') . ' - ' . 
                               Carbon::parse($user->peserta->periode_end)->format('F'),
                    'sisaHari' => max(0, $sisaHari),
                    'status' => 'Aktif'
                ];
            })
            ->values();
        
        return response()->json([
            'room' => $room->nama_room,
            'peserta' => $data
        ]);
    }
}