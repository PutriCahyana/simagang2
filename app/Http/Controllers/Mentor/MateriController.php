<?php

namespace App\Http\Controllers\Mentor;

use App\Models\Room;
use App\Models\Materi;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png,gif,mp4,avi,mov,zip,rar',
        ],[
            'judul.required' => 'Judul Harus Diisi',
            'room_id.required' => 'Kategori Harus dipilih',
            'konten.required' => 'Materi Harus memiliki konten',
            'file.max' => 'Ukuran file maksimal 10MB',
            'file.mimes' => 'Format file tidak didukung',
        ]);

        $materi = new Materi;
        $materi->judul = $request->judul;
        $materi->room_id = $request->room_id;
        $materi->deskripsi = $request->deskripsi;
        $materi->konten = $request->konten;

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('materi_files', $fileName, 'public');
            $materi->file_path = $filePath;
        }

        $materi->save();

        Activity::create([
            'user_id' => Auth::id(),
            'room_id' => $request->room_id,
            'type' => 'task_added',
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
            'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png,gif,mp4,avi,mov,zip,rar',
        ],[
            'judul.required' => 'Judul Harus Diisi',
            'room_id.required' => 'Kategori Harus dipilih',
            'konten.required' => 'Materi Harus memiliki konten',
            'file.max' => 'Ukuran file maksimal 10MB',
            'file.mimes' => 'Format file tidak didukung',
        ]);

        $materi = Materi::findOrFail($id);
        $materi->judul = $request->judul;
        $materi->room_id = $request->room_id;
        $materi->deskripsi = $request->deskripsi;
        $materi->konten = $request->konten;

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($materi->file_path && Storage::exists('public/' . $materi->file_path)) {
                Storage::delete('public/' . $materi->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('materi_files', $fileName, 'public');
            $materi->file_path = $filePath;
        }

        // Handle remove file
        if ($request->has('remove_file') && $request->remove_file == '1') {
            if ($materi->file_path && Storage::exists('public/' . $materi->file_path)) {
                Storage::delete('public/' . $materi->file_path);
            }
            $materi->file_path = null;
        }

        $materi->save();

        return redirect()->route('mentor.materi')->with('success', 'Materi Berhasil Diupdate!');
    }

    public function destroy($id){
        $materi = Materi::findOrFail($id);
        
        // Delete file if exists
        if ($materi->file_path && Storage::exists('public/' . $materi->file_path)) {
            Storage::delete('public/' . $materi->file_path);
        }
        
        $materi->delete();

        return redirect()->route('mentor.materi')->with('success', 'Materi Berhasil Dihapus!');
    }

    public function downloadFile($id)
    {
        $materi = Materi::findOrFail($id);
        
        if (!$materi->file_path) {
            return redirect()->back()->with('error', 'Materi ini tidak memiliki file lampiran!');
        }

        $filePath = storage_path('app/public/' . $materi->file_path);
        
        // Cek apakah file ada
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di server!');
        }

        // Get original filename
        $fileName = basename($materi->file_path);
        
        return response()->download($filePath, $fileName);
    }
}