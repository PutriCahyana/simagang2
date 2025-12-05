<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Room; // pastikan model Room sudah ada

class RoomController extends Controller{

    public function show($room_id)
    {
        $user = Auth::user();
        $room = Room::findOrFail($room_id);

        // Optional: Cek apakah user sudah join room ini
        // if (!$user->joinedRooms->contains($room->id)) {
        //     abort(403, 'Anda tidak memiliki akses ke room ini');
        // }

        $data = [
            "judul" => $room->name,
            "menuPesertaRoom" => "active",
            "user" => $user,
            "room" => $room,
        ];

        return view('peserta.room.show', $data);
    }

}