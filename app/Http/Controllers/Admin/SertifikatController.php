<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\User;
use App\Models\Certificate;
use Illuminate\Http\Request;
use App\Models\CertificateSetting;
use App\Http\Controllers\Controller;
use Spatie\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Facades\Storage;

class SertifikatController extends Controller
{
    public function index()
    {
        // Ambil semua user dengan role peserta yang eligible (sudah selesai magang)
        $allPeserta = User::where('role', 'peserta')
            ->with(['peserta', 'joinedRooms'])
            ->whereHas('peserta', function($query) {
                $query->whereNotNull('periode_end')
                      ->whereNotNull('periode_start');
            })
            ->get();
        
        // Pisahkan berdasarkan status sertifikat
        $pesertaBelum = $allPeserta->filter(function($user) {
            return !Certificate::where('user_id', $user->id)->exists();
        })->map(function($user) {
            // Add periode_label for easier filtering
            if ($user->peserta) {
                $start = Carbon::parse($user->peserta->periode_start);
                $end = Carbon::parse($user->peserta->periode_end);
                $user->peserta->periode_label = $start->format('M Y') . ' - ' . $end->format('M Y');
            }
            return $user;
        });
        
        // Draft certificates
        $pesertaDraft = Certificate::where('status', 'draft')
            ->with(['user.peserta', 'user.joinedRooms'])
            ->get()
            ->map(function($cert) {
                if ($cert->user && $cert->user->peserta) {
                    $start = Carbon::parse($cert->user->peserta->periode_start);
                    $end = Carbon::parse($cert->user->peserta->periode_end);
                    $cert->user->peserta->periode_label = $start->format('M Y') . ' - ' . $end->format('M Y');
                }
                return $cert;
            });
        
        // Approved certificates
        $pesertaApproved = Certificate::where('status', 'approved')
            ->with(['user.peserta', 'user.joinedRooms'])
            ->orderBy('approved_at', 'desc')
            ->get()
            ->map(function($cert) {
                if ($cert->user && $cert->user->peserta) {
                    $start = Carbon::parse($cert->user->peserta->periode_start);
                    $end = Carbon::parse($cert->user->peserta->periode_end);
                    $cert->user->peserta->periode_label = $start->format('M Y') . ' - ' . $end->format('M Y');
                }
                return $cert;
            });
        
        // Get unique periode list for filter
        $periodeList = $allPeserta->map(function($user) {
            if ($user->peserta) {
                $start = Carbon::parse($user->peserta->periode_start);
                $end = Carbon::parse($user->peserta->periode_end);
                return $start->format('M Y') . ' - ' . $end->format('M Y');
            }
            return null;
        })->filter()->unique()->values();
        
        // Get all rooms for filter
        $fungsiList = Room::all();
        
        return view('admin.sertifikat.index', [
            'pesertaBelum' => $pesertaBelum,
            'pesertaDraft' => $pesertaDraft,
            'pesertaApproved' => $pesertaApproved,
            'belumGenerate' => $pesertaBelum->count(),
            'draft' => $pesertaDraft->count(),
            'approved' => $pesertaApproved->count(),
            'periodeList' => $periodeList,
            'fungsiList' => $fungsiList
        ]);
    }
    
    public function settings()
    {
        $settings = CertificateSetting::getSettings();
        return view('admin.sertifikat.settings',[
            'settings' => $settings
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'nomor_counter' => 'required|integer|min:1',
            'format_tetap' => 'required|string|max:50',
            'suffix' => 'required|string|max:20',
            'pjs_nama' => 'required|string|max:255',
            'pjs_jabatan' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
        ]);
        
        $settings = CertificateSetting::first();
        
        if (!$settings) {
            $settings = new CertificateSetting();
        }
        
        // Update data
        $settings->nomor_counter = $validated['nomor_counter'];
        $settings->format_tetap = $validated['format_tetap'];
        $settings->suffix = $validated['suffix'];
        $settings->pjs_nama = $validated['pjs_nama'];
        $settings->pjs_jabatan = $validated['pjs_jabatan'];
        $settings->lokasi = $validated['lokasi'];
        
        $settings->save();
        
        return redirect()->route('admin.sertifikat.settings')
            ->with('success', 'Settings sertifikat berhasil diperbarui');
    }
    
    public function generate(Request $request)
    {
        // Halaman untuk generate sertifikat (will be created next)
        $ids = $request->input('ids', []);
        
        $pesertaList = User::whereIn('id', $ids)
            ->with(['peserta', 'joinedRooms'])
            ->get();
        
        return view('admin.sertifikat.generate', [
            'pesertaList' => $pesertaList
        ]);
    }

    public function storeGenerate(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'predikat' => 'required|array',
            'predikat.*' => 'in:CUKUP BAIK,BAIK,SANGAT BAIK'
        ]);
        
        $userIds = $validated['user_ids'];
        $predikat = $validated['predikat'];
        $generated = 0;
        
        foreach ($userIds as $userId) {
            // Check if certificate already exists
            if (Certificate::where('user_id', $userId)->exists()) {
                continue;
            }
            
            // Generate nomor surat
            $nomorSurat = CertificateSetting::generateNomorSurat();
            
            // Create certificate as draft
            Certificate::create([
                'user_id' => $userId,
                'nomor_surat' => $nomorSurat,
                'predikat' => $predikat[$userId] ?? 'BAIK',
                'tanggal_terbit' => now(),
                'status' => 'draft'
            ]);
            
            $generated++;
        }
        
        return redirect()->route('admin.sertifikat.index')
            ->with('success', "$generated sertifikat berhasil di-generate sebagai draft. Silakan review sebelum approve.");
    }
        
    public function review(Request $request)
    {
        // Halaman untuk review dan approve sertifikat draft
        $ids = $request->input('ids', []);
        
        $certificates = Certificate::whereIn('id', $ids)
            ->where('status', 'draft')
            ->with(['user.peserta', 'user.joinedRooms'])
            ->get();
        
        // Tambahkan baris ini!
        $settings = CertificateSetting::getSettings();
        
        return view('admin.sertifikat.review', [
            'certificates' => $certificates,
            'settings' => $settings
        ]);
    }
    
    public function preview($id)
    {
        // Preview sertifikat yang sudah approved
        $certificate = Certificate::where('id', $id)
            ->where('status', 'approved')
            ->with(['user.peserta', 'user.joinedRooms'])
            ->firstOrFail();
        
        return view('admin.sertifikat.preview', [
            'certificate' => $certificate
        ]);
    }
    
    public function deleteBulk(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada sertifikat yang dipilih');
        }
        
        $deleted = Certificate::whereIn('id', $ids)
            ->where('status', 'draft')
            ->delete();
        
        return redirect()->route('admin.sertifikat.index')
            ->with('success', "$deleted sertifikat draft berhasil dihapus");
    }

    public function approve(Request $request)
    {
        $validated = $request->validate([
            'certificate_ids' => 'required|array',
            'certificate_ids.*' => 'exists:certificates,id'
        ]);
        
        $certificateIds = $validated['certificate_ids'];
        $approved = 0;
        
        foreach ($certificateIds as $certId) {
            $certificate = Certificate::where('id', $certId)
                ->where('status', 'draft')
                ->with(['user.peserta', 'user.joinedRooms'])
                ->first();
            
            if ($certificate) {
                $settings = CertificateSetting::first();
                
                // Generate filename
                $filename = 'certificates/' . str_replace('/', '-', $certificate->nomor_surat) . '.pdf';
                $fullPath = storage_path('app/public/' . $filename);
                
                // Pastikan folder ada
                $directory = dirname($fullPath);
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }
                
                // Generate dan save PDF
                Pdf::view('templates.certificate-pdf', [
                        'certificate' => $certificate,
                        'settings' => $settings
                    ])
                    ->format('a4')
                    ->landscape()
                    ->withBrowsershot(function ($browsershot) {
                            $browsershot->noSandbox()
                                    ->setOption('args', ['--disable-web-security'])
                                    ->waitUntilNetworkIdle();
                        })
                    ->save($fullPath); // Tambahkan parameter path di sini
                
                // Update certificate
                $certificate->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'pdf_data' => $filename
                ]);
                
                $approved++;
            }
        }
        
        return redirect()->route('admin.sertifikat.index')
            ->with('success', "$approved sertifikat berhasil di-approve dan tersedia untuk peserta.");
    }
}