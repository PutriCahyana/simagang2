<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Room; // pastikan model Room sudah ada

class RoomController extends Controller
{
    public function index(){
         $data = array(
            "judul" => "Room",
            "menuAdminRoom" => "active",
            "rooms" => Room::with('mentor.user')->get()
        );
        return view('admin/room/index', $data);
    }

    public function create(){
         $data = array(
            "judul" => "Add Room",
            "menuAdminRoom" => "active",
            
        );
        return view('admin/room/create', $data);
    }

    public function store(Request $request)
    {
        // validasi input
        $request->validate([
            'nama_room' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        // simpan ke database
        Room::create([
            'nama_room' => $request->nama_room,
            'deskripsi' => $request->deskripsi,
            'mentor_id' => null, // kalau room terhubung ke mentor yg login
        ]);

        // redirect balik ke halaman room dengan pesan sukses
        return redirect()->route('room')->with('success', 'Room berhasil ditambahkan!');
    }

    public function edit($room_id)
    {
        $room = Room::where('room_id', $room_id)->firstOrFail();
        
        $data = array(
            "judul" => "Edit Room",
            "menuAdminRoom" => "active",
            "room" => $room
        );
        
        return view('admin/room/edit', $data);
    }

    public function update(Request $request, $room_id)
    {
        $room = Room::where('room_id', $room_id)->firstOrFail();
        
        // validasi input
        $request->validate([
            'nama_room' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        // update data
        $room->update([
            'nama_room' => $request->nama_room,
            'deskripsi' => $request->deskripsi,
        ]);

        // redirect balik dengan pesan sukses
        return redirect()->route('room')->with('success', 'Room berhasil diupdate!');
    }

    public function destroy($room_id)
{
    $room = Room::where('room_id', $room_id)->firstOrFail();
    
    // Hapus room
    $room->delete();
    
    // Redirect dengan pesan sukses
    return redirect()->route('room')->with('success', 'Room berhasil dihapus!');
}
}
