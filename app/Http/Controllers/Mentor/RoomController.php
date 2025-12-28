<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\mentor;
use App\Models\User;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    public function index()
    {
        $data = [
            "judul" => "Room",
            "menuMentorRoom" => "active",
            // tampilkan hanya room yang dibuat oleh mentor yang login
            "rooms" => Room::with('mentor.user')
            ->where('mentor_id', Auth::user()->mentorProfile->mentor_id ?? null)
            ->get(),

        ];

        return view('mentor.room.index', $data);
    }

    public function create()
    {
        $data = [
            "judul" => "Add Room",
            "menuMentorRoom" => "active",
        ];

        return view('mentor.room.create', $data);
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
            'nama_room'  => $request->nama_room,
            'deskripsi'  => $request->deskripsi,
            'mentor_id'  => Auth::user()->mentorProfile->mentor_id ?? null,
        ]);

        // redirect balik ke halaman room dengan pesan sukses
        return redirect()->route('mentor.room')->with('success', 'Room berhasil ditambahkan!');
    }

    public function edit($id)
    {
        // cari room berdasarkan room_id
        $room = Room::where('room_id', $id)->firstOrFail();

        // pastikan room ini milik mentor yang login
        if ($room->mentor_id != (Auth::user()->mentorProfile->mentor_id ?? null)) {
            return redirect()->route('mentor.room')->with('error', 'Anda tidak memiliki akses untuk mengedit room ini!');
        }

        // return data room dalam bentuk JSON untuk modal
        return response()->json($room);
    }

    public function update(Request $request, $id)
    {
        // validasi input
        $request->validate([
            'nama_room' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        // cari room berdasarkan room_id
        $room = Room::where('room_id', $id)->firstOrFail();

        // pastikan room ini milik mentor yang login
        if ($room->mentor_id != (Auth::user()->mentorProfile->mentor_id ?? null)) {
            return redirect()->route('mentor.room')->with('error', 'Anda tidak memiliki akses untuk mengedit room ini!');
        }

        // update data room
        $room->update([
            'nama_room' => $request->nama_room,
            'deskripsi' => $request->deskripsi,
        ]);

        // redirect dengan pesan sukses
        return redirect()->route('mentor.room')->with('success', 'Room berhasil diupdate!');
    }

    public function destroy($id)
    {
        // cari room berdasarkan room_id (bukan id)
        $room = Room::where('room_id', $id)->firstOrFail();

        // pastikan room ini milik mentor yang login
        if ($room->mentor_id != (Auth::user()->mentorProfile->mentor_id ?? null)) {
            return redirect()->route('mentor.room')->with('error', 'Anda tidak memiliki akses untuk menghapus room ini!');
        }

        // hapus room
        $room->delete();

        // redirect dengan pesan sukses
        return redirect()->route('mentor.room')->with('success', 'Room berhasil dihapus!');
    }
}