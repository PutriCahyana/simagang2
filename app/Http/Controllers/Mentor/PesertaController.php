<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesertaController extends Controller
{
    public function index()
    {
        $mentor = Auth::user();
        
        // Ambil semua room yang di-MANAGE oleh mentor ini
        $rooms = $mentor->managedRooms()->get();
        
        // Ambil semua peserta dari room-room tersebut
        $pesertaCollection = collect();
        
        foreach ($rooms as $room) {
            $pesertaFromRoom = $room->peserta()
                ->with(['peserta', 'joinedRooms'])
                ->get()
                ->map(function ($user) use ($room) {
                    $user->current_room = $room;
                    return $user;
                });
            
            $pesertaCollection = $pesertaCollection->merge($pesertaFromRoom);
        }
        
        // Grup peserta berdasarkan user_id
        $pesertaGrouped = $pesertaCollection->groupBy('id')->map(function ($items) {
            $firstItem = $items->first();
            $firstItem->all_rooms = $items->pluck('current_room')->unique('room_id');
            return $firstItem;
        })->values();
        
        return view('mentor.peserta.index', [
            'pesertaList' => $pesertaGrouped,
            'totalPeserta' => $pesertaGrouped->count(),
            'totalRooms' => $rooms->count(),
            'allRooms' => $rooms // ← TAMBAH INI untuk kirim semua room mentor
        ]);
    }
    
    public function show($id)
{
    $mentor = Auth::user();
    
    // Ambil room_id yang di-manage oleh mentor
    $roomIds = $mentor->managedRooms()->pluck('room_id');
    
    $peserta = \App\Models\User::whereHas('joinedRooms', function ($query) use ($roomIds) {
            $query->whereIn('room.room_id', $roomIds); // ← TAMBAH: 'room.' untuk spesifik tabel room
        })
        ->where('id', $id)
        ->where('role', 'peserta')
        ->with(['peserta', 'joinedRooms'])
        ->firstOrFail();
    
    return view('mentor.peserta.show', [
        'peserta' => $peserta
    ]);
}
}