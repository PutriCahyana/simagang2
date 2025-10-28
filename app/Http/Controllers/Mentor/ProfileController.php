<?php

namespace App\Http\Controllers\Mentor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    /**
     * Show profile form
     */
    public function index()
    {
        $user = auth()->user();
        $mentor = $user->mentor;

        return view('mentor.profile', compact('user', 'mentor'));
    }

    /**
     * Update profile
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        $mentor = $user->mentor;

        $request->validate([
            'nama' => 'required|string|max:255',
            'handphone' => 'nullable|string|max:15',
            'signature' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        // Update user
        $user->update([
            'nama' => $request->nama,
        ]);

        // Update mentor
        $mentorData = [
            'handphone' => $request->handphone,
        ];

        // Handle signature upload
        if ($request->hasFile('signature')) {
            // Delete old signature
            if ($mentor->signature_path && Storage::disk('public')->exists($mentor->signature_path)) {
                Storage::disk('public')->delete($mentor->signature_path);
            }

            // Upload new signature
            $path = $request->file('signature')->store('signatures', 'public');
            $mentorData['signature_path'] = $path;
        }

        $mentor->update($mentorData);

        return back()->with('success', 'Profil berhasil diupdate!');
    }

    /**
     * Delete signature
     */
    public function deleteSignature()
    {
        $mentor = auth()->user()->mentor;

        if ($mentor->signature_path && Storage::disk('public')->exists($mentor->signature_path)) {
            Storage::disk('public')->delete($mentor->signature_path);
        }

        $mentor->update(['signature_path' => null]);

        return back()->with('success', 'Tanda tangan berhasil dihapus!');
    }
}