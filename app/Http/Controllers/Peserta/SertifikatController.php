<?php

namespace App\Http\Controllers\Peserta;

use App\Models\Certificate;
use Illuminate\Http\Request;
use App\Models\CertificateSetting;
use App\Http\Controllers\Controller;

class SertifikatController extends Controller
{
    /**
     * Cek status sertifikat peserta
     * Jika approved → redirect ke preview
     * Jika belum/draft → tampilkan timeline
     */

    public function statusAjax()
    {
        $user = auth()->user();
        $certificate = Certificate::where('user_id', $user->id)->first();
        
        // Jika sudah approved, tampilkan button download
        if ($certificate && $certificate->status === 'approved') {
            $html = view('peserta.sertifikat.status-approved', compact('certificate'))->render();
        } else {
            // Tampilkan timeline
            $html = view('peserta.sertifikat.status-timeline', compact('certificate', 'user'))->render();
        }
        
        return response()->json(['html' => $html]);
    }

    public function status()
    {
        $user = auth()->user();
        
        // Cek apakah ada sertifikat
        $certificate = Certificate::where('user_id', $user->id)->first();
        
        // Jika sudah approved, redirect langsung ke preview
        if ($certificate && $certificate->status === 'approved') {
            return redirect()->route('peserta.sertifikat.preview');
        }
        
        // Jika belum atau masih draft, tampilkan timeline
        return view('peserta.sertifikat.status', [
            'certificate' => $certificate,
            'user' => $user
        ]);
    }

    /**
     * Preview sertifikat peserta yang sudah approved
     */
    public function preview()
    {
        $user = auth()->user();
        
        $certificate = Certificate::where('user_id', $user->id)
            ->where('status', 'approved')
            ->with(['user.peserta', 'user.joinedRooms'])
            ->firstOrFail();
        
        $settings = CertificateSetting::first();
        
        return view('peserta.sertifikat.preview', [
            'certificate' => $certificate,
            'settings' => $settings
        ]);
    }

    /**
     * Download sertifikat peserta
     */
    public function download()
    {
        $user = auth()->user();
        
        $certificate = Certificate::where('user_id', $user->id)
            ->where('status', 'approved')
            ->with(['user.peserta', 'user.joinedRooms'])
            ->firstOrFail();
        
        // TODO: Generate PDF pakai DomPDF atau library lain
        // Untuk sekarang redirect ke preview dulu
        return redirect()->route('peserta.sertifikat.preview')
            ->with('info', 'Silakan gunakan tombol Print untuk menyimpan sebagai PDF');
    }
}