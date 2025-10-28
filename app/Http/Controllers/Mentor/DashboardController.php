<?php

namespace App\Http\Controllers\Mentor;

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
        // Ambil data mentor yang sedang login
        $mentor = Auth::user();
        
        // Ambil semua room yang dibimbing mentor ini
        // Kita ambil room pertama dulu (atau bisa disesuaikan kalau mentor punya banyak room)
        $rooms = Room::where('mentor_id', $mentor->id)->get();
        $menuDashboard = "active";
        // Untuk contoh, kita ambil data semua room yang dibimbing
        // Atau bisa filter ke room tertentu
        $roomData = [];
        $allPeserta = collect();
        $allPesertaSelesai = collect();
        
        foreach ($rooms as $room) {
            // Ambil peserta aktif di room ini
            $pesertaActive = $room->peserta()
                ->with('peserta')
                ->get()
                ->filter(function($user) {
                    return $user->peserta && $user->peserta->periode_end >= Carbon::now();
                });
            
            $allPeserta = $allPeserta->merge($pesertaActive);
            
            // Ambil peserta yang sudah selesai
            $pesertaCompleted = $room->peserta()
                ->with('peserta')
                ->get()
                ->filter(function($user) {
                    return $user->peserta && $user->peserta->periode_end < Carbon::now();
                });
            
            $allPesertaSelesai = $allPesertaSelesai->merge($pesertaCompleted);
        }
        
        //testingkjsdhwhka
        // Total peserta aktif
        $totalPeserta = $allPeserta->count();
        
        // Data peserta dengan detail
        $pesertaData = $allPeserta->map(function($user) use ($rooms) {
            $pesertaDetail = $user->peserta;
            $sisaHari = (int) Carbon::now()->diffInDays($pesertaDetail->periode_end, false);
            
            // Cari room peserta ini (ambil yang pertama kalau ada di banyak room)
            $userRoom = $rooms->first(function($room) use ($user) {
                return $room->users->contains($user->id);
            });
            
            return [
                'nama' => $user->nama,
                'institut' => $pesertaDetail->institut,
                'periode' => Carbon::parse($pesertaDetail->periode_start)->format('F') . ' - ' . 
                           Carbon::parse($pesertaDetail->periode_end)->format('F'),
                'periode_start' => $pesertaDetail->periode_start,
                'periode_end' => $pesertaDetail->periode_end,
                'sisaHari' => max(0, $sisaHari),
                'status' => 'Aktif',
                'room_nama' => $userRoom ? $userRoom->nama_room : '-'
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
        $pesertaSelesaiData = $allPesertaSelesai->map(function($user) {
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
        
        // Statistik singkat
        $stats = [
            'peserta_aktif' => $totalPeserta,
            'peserta_selesai' => $allPesertaSelesai->count(),
            'periode_berjalan' => $periodeData->count(),
            'institut_berbeda' => $institutActive->count(),
            'total_rooms' => $rooms->count()
        ];
        
        // Ambil room pertama untuk ditampilkan (atau bisa disesuaikan)
        $primaryRoom = $rooms->first();
        $mentorRoom = $primaryRoom ? $primaryRoom->nama_room : '-';
        
        return view('mentor.dashboard', compact(
            'mentor',
            'mentorRoom',
            'rooms',
            'totalPeserta',
            'pesertaData',
            'periodeData',
            'institutActive',
            'institutCompleted',
            'stats'
        ));
    }
    
    // API untuk modal detail peserta per periode
    public function getPeriodDetail($periode)
    {
        $mentor = Auth::user();
        $rooms = Room::where('mentor_id', $mentor->id)->get();
        
        $data = collect();
        
        foreach ($rooms as $room) {
            $peserta = $room->peserta()
                ->with('peserta')
                ->get()
                ->filter(function($user) use ($periode) {
                    if (!$user->peserta) return false;
                    
                    $userPeriode = Carbon::parse($user->peserta->periode_start)->format('F') . ' - ' . 
                                  Carbon::parse($user->peserta->periode_end)->format('F');
                    
                    return $userPeriode === $periode && 
                           $user->peserta->periode_end >= Carbon::now();
                })
                ->map(function($user) {
                    $sisaHari = Carbon::now()->diffInDays($user->peserta->periode_end, false);
                    return [
                        'nama' => $user->nama,
                        'institut' => $user->peserta->institut,
                        'sisaHari' => max(0, $sisaHari),
                        'status' => 'Aktif'
                    ];
                });
            
            $data = $data->merge($peserta);
        }
        
        return response()->json($data->values());
    }
    
    // API untuk modal detail peserta per institut
    public function getInstitutDetail($institut, $type)
    {
        $mentor = Auth::user();
        $rooms = Room::where('mentor_id', $mentor->id)->get();
        
        $data = collect();
        
        foreach ($rooms as $room) {
            if ($type === 'active') {
                $peserta = $room->peserta()
                    ->with('peserta')
                    ->get()
                    ->filter(function($user) use ($institut) {
                        return $user->peserta && 
                               $user->peserta->institut === $institut &&
                               $user->peserta->periode_end >= Carbon::now();
                    })
                    ->map(function($user) {
                        $sisaHari = Carbon::now()->diffInDays($user->peserta->periode_end, false);
                        return [
                            'nama' => $user->nama,
                            'periode' => Carbon::parse($user->peserta->periode_start)->format('F') . ' - ' . 
                                       Carbon::parse($user->peserta->periode_end)->format('F'),
                            'sisaHari' => max(0, $sisaHari),
                            'status' => 'Aktif'
                        ];
                    });
            } else {
                $peserta = $room->peserta()
                    ->with('peserta')
                    ->get()
                    ->filter(function($user) use ($institut) {
                        return $user->peserta && 
                               $user->peserta->institut === $institut &&
                               $user->peserta->periode_end < Carbon::now();
                    })
                    ->map(function($user) {
                        return [
                            'nama' => $user->nama,
                            'periode' => Carbon::parse($user->peserta->periode_start)->format('F Y') . ' - ' . 
                                       Carbon::parse($user->peserta->periode_end)->format('F Y'),
                            'selesai' => Carbon::parse($user->peserta->periode_end)->format('F Y'),
                            'status' => 'Selesai'
                        ];
                    });
            }
            
            $data = $data->merge($peserta);
        }
        
        return response()->json($data->values());
    }
    
    // API untuk modal detail semua peserta di room
    public function getRoomDetail()
    {
        $mentor = Auth::user();
        $rooms = Room::where('mentor_id', $mentor->id)->get();
        
        $data = collect();
        
        foreach ($rooms as $room) {
            $peserta = $room->peserta()
                ->with('peserta')
                ->get()
                ->filter(function($user) {
                    return $user->peserta && $user->peserta->periode_end >= Carbon::now();
                })
                ->map(function($user) use ($room) {
                    $sisaHari = Carbon::now()->diffInDays($user->peserta->periode_end, false);
                    return [
                        'nama' => $user->nama,
                        'institut' => $user->peserta->institut,
                        'periode' => Carbon::parse($user->peserta->periode_start)->format('F') . ' - ' . 
                                   Carbon::parse($user->peserta->periode_end)->format('F'),
                        'sisaHari' => max(0, $sisaHari),
                        'status' => 'Aktif',
                        'room' => $room->nama_room
                    ];
                });
            
            $data = $data->merge($peserta);
        }
        
        return response()->json($data->values());
    }
    
    // API untuk detail per room (jika mentor punya banyak room)
    public function getRoomDetailById($roomId)
    {
        $mentor = Auth::user();
        $room = Room::where('mentor_id', $mentor->id)
                    ->where('room_id', $roomId)
                    ->firstOrFail();
        
        $data = $room->peserta()
            ->with('peserta')
            ->get()
            ->filter(function($user) {
                return $user->peserta && $user->peserta->periode_end >= Carbon::now();
            })
            ->map(function($user) {
                $sisaHari = Carbon::now()->diffInDays($user->peserta->periode_end, false);
                return [
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