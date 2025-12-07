<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Room;
use Illuminate\Http\Request;

class PesertaController extends Controller
{
    public function index()
    {
        // Ambil semua user dengan role peserta
        $pesertaList = User::where('role', 'peserta')
            ->with(['peserta', 'joinedRooms'])
            ->get()
            ->map(function ($user) {
                // Ambil semua room yang diikuti peserta
                $user->all_rooms = $user->joinedRooms;
                return $user;
            });
        
        // Ambil semua room untuk filter
        $allRooms = Room::all();
        
        return view('admin.peserta.index', [
            'pesertaList' => $pesertaList,
            'totalPeserta' => $pesertaList->count(),
            'totalRooms' => $allRooms->count(),
            'allRooms' => $allRooms
        ]);
    }
    
    public function show($id)
    {
        // Ambil peserta berdasarkan id tanpa filter room
        $peserta = User::where('id', $id)
            ->where('role', 'peserta')
            ->with(['peserta', 'joinedRooms'])
            ->firstOrFail();
        
        return view('admin.peserta.show', [
            'peserta' => $peserta
        ]);
    }

    public function destroy($id)
    {
        $peserta = User::where('id', $id)
            ->where('role', 'peserta')
            ->firstOrFail();
        
        $nama = $peserta->nama;
        
        // Hapus relasi di pivot table room_user
        $peserta->joinedRooms()->detach();
        
        // Hapus data peserta di tabel peserta
        if ($peserta->peserta) {
            $peserta->peserta->delete();
        }
        
        // Hapus user
        $peserta->delete();
        
        return redirect()->route('admin.peserta.index')
            ->with('success', "Peserta $nama berhasil dihapus dari sistem");
    }
}