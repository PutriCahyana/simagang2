<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Materi;
use Illuminate\Http\Request;

class MateriController extends Controller
{
    public function index(){
         $data = array(
            "judul" => "Materi",
            "menuAdminMateri" => "active",
            "menuMentorMateri" => "active",
            "materi" => Materi::with('room')->get()
        );
        return view('admin/materi/index', $data);
    }

    public function create(){
         $data = array(
            "judul" => "Add Materi",
            "menuAdminMateri" => "active",
            "menuMentorMateri" => "active",
            "room" => Room::all(),
        );
        return view('admin/materi/create', $data);
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

        return redirect()->route('materi')->with('success', 'Materi Berhasil Dibuat!');
    }

    public function show($id){
        $materi = Materi::with('room')->findOrFail($id);
        
        $data = array(
            "judul" => "Detail Materi",
            "menuAdminMateri" => "active",
            "menuMentorMateri" => "active",
            "materi" => $materi
        );
        return view('admin/materi/view', $data);
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
        return view('admin/materi/edit', $data);
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

        return redirect()->route('materi')->with('success', 'Materi Berhasil Diupdate!');
    }

    public function destroy($id){
        $materi = Materi::findOrFail($id);
        $materi->delete();

        return redirect()->route('materi')->with('success', 'Materi Berhasil Dihapus!');
    }
}