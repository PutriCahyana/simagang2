<?php

namespace App\Http\Controllers\Mentor;

use App\Models\Room;

use App\Models\Materi;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MateriController extends Controller
{
    public function index(){
         $data = array(
            "judul" => "Materi",
            "menuAdminMateri" => "active",
            "menuMentorMateri" => "active",
            "materi" => Materi::with('room')->get()
        );
        return view('mentor/materi/index', $data);
    }

    public function create(){
         $data = array(
            "judul" => "Add Materi",
            "menuAdminMateri" => "active",
            "menuMentorMateri" => "active",
            "room" => Room::all(),
        );
        return view('mentor/materi/create', $data);
    }

    public function store(Request $request){
        $request->validate([
            'judul' => 'required|string|max:100',
            'room_id' => 'required',
            'deskripsi' => 'nullable|string|max:200',
            'konten' => 'required|string',
        ],[
            'judul' => 'Judul Harus Diisi',
            'room_id'   => 'Kategori Harus dipilih',
            'konten'    => 'Materi Harus memiliki konten',
        ]);

        $materi = new Materi;
        $materi->judul = $request->judul;
        $materi->room_id = $request->room_id;
        $materi->deskripsi = $request->deskripsi;
        $materi->konten = $request->konten;
        $materi->save();

         Activity::create([
            'user_id' => Auth::id(),
            'room_id' => $request->room_id,
            'type' => 'task_added',  // bisa ganti jadi 'materi_added' kalau perlu
            'description' => 'New Material: ' . $materi->judul,
        ]);

        return redirect()->route('mentor.materi')->with('success', 'Materi Berhasil Dibuat!');

    }

    public function show($id){
        $materi = Materi::with('room')->findOrFail($id);
        
        $data = array(
            "judul" => "Detail Materi",
            "menuAdminMateri" => "active",
            "menuMentorMateri" => "active",
            "materi" => $materi
        );
        return view('mentor/materi/view', $data);
    }

    public function edit($id){
        $materi = Materi::findOrFail($id);
        
        $data = array(
            "judul" => "Edit Materi",
            "menuAdminMateri" => "active",
            "menuMentorMateri" => "active",
            "materi" => $materi,
            "room" => Room::all(),
        );
        return view('mentor/materi/edit', $data);
    }

    public function update(Request $request, $id){
        $request->validate([
            'judul' => 'required|string|max:100',
            'room_id' => 'required',
            'deskripsi' => 'nullable|string|max:200',
            'konten' => 'required|string',
        ],[
            'judul' => 'Judul Harus Diisi',
            'room_id'   => 'Kategori Harus dipilih',
            'konten'    => 'Materi Harus memiliki konten',
        ]);

        $materi = Materi::findOrFail($id);
        $materi->judul = $request->judul;
        $materi->room_id = $request->room_id;
        $materi->deskripsi = $request->deskripsi;
        $materi->konten = $request->konten;
        $materi->save();

        return redirect()->route('mentor.materi')->with('success', 'Materi Berhasil Diupdate!');
    }

    public function destroy($id){
        $materi = Materi::findOrFail($id);
        $materi->delete();

        return redirect()->route('mentor.materi')->with('success', 'Materi Berhasil Dihapus!');
    }
}

