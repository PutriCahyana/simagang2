<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        return view('admin.profile.profile', compact('user'));
    }

    public function updateDataDiri(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        
        // Update user
        $user->update([
            'nama' => $request->nama,
        ]);

        return redirect()->back()->with('success', 'Data diri berhasil diperbarui!');
    }

    public function updateUsername(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . Auth::id(),
        ]);

        $user = Auth::user();
        $user->update([
            'username' => $request->username,
        ]);

        return redirect()->back()->with('success', 'Username berhasil diubah!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Cek password lama
        if (!Hash::check($request->password_lama, $user->password)) {
            return redirect()->back()->withErrors(['password_lama' => 'Password lama tidak sesuai']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password_baru),
        ]);

        return redirect()->back()->with('success', 'Password berhasil diubah!');
    }

    public function updateFotoProfil(Request $request)
    {
        $request->validate([
            'foto_profil' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Auth::user();

        // Hapus foto lama jika ada
        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        // Upload foto baru
        $path = $request->file('foto_profil')->store('foto_profil', 'public');

        // Update database
        $user->update([
            'foto_profil' => $path,
        ]);

        return redirect()->back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    public function deleteFotoProfil()
    {
        $user = Auth::user();

        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $user->update([
            'foto_profil' => null,
        ]);

        return redirect()->back()->with('success', 'Foto profil berhasil dihapus!');
    }
}