<?php
namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PesertaDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get room peserta (ambil room pertama karena peserta bisa join multiple rooms)
        $room = $user->rooms()->with('mentor.user')->first();
        
        // Hitung periode magang
        $peserta = $user->peserta;
        $periodeStart = $peserta ? Carbon::parse($peserta->periode_start)->startOfDay() : null;
        $periodeEnd = $peserta ? Carbon::parse($peserta->periode_end)->endOfDay() : null;
        
        // 🔥 PERBAIKAN: Hitung progress berdasarkan HARI yang sudah berlalu
        $totalWeeks = 0;
        $currentWeek = 0;
        $progress = 0;
        
        if ($periodeStart && $periodeEnd) {
            $today = now()->startOfDay();
            
            // Total hari dalam periode magang
            $totalDays = $periodeStart->diffInDays($periodeEnd) + 1; // +1 agar inclusive
            $totalWeeks = max(1, ceil($totalDays / 7)); // Konversi ke minggu untuk display
            
            // Jika sekarang masih dalam periode magang
            if ($today->between($periodeStart, $periodeEnd)) {
                // Hitung hari yang sudah berlalu (hari pertama = 0)
                $daysElapsed = max(0, $periodeStart->diffInDays($today)); // TANPA +1
                $currentWeek = $daysElapsed > 0 ? ceil($daysElapsed / 7) : 1; // Tetap Week 1 di hari pertama
                $progress = $totalDays > 0 ? round(($daysElapsed / $totalDays) * 100) : 0;
            } 
            // Jika belum mulai magang
            elseif ($today->lt($periodeStart)) {
                $currentWeek = 0;
                $progress = 0;
            }
            // Jika sudah selesai magang
            else {
                $currentWeek = $totalWeeks;
                $progress = 100;
            }
        }
        
        // 🔥 PERBAIKAN: Hitung total hari kerja dari periode_start sampai periode_end (BUKAN sampai sekarang!)
        $totalWorkDays = 0;
        if ($periodeStart && $periodeEnd) {
            $start = $periodeStart->copy();
            $end = $periodeEnd->copy();
            
            while ($start->lte($end)) {
                // Skip Saturday (6) dan Sunday (0)
                if (!in_array($start->dayOfWeek, [0, 6])) {
                    $totalWorkDays++;
                }
                $start->addDay();
            }
        }
        
        // Stats: Total Logbook
        $totalLogbooks = $user->logbooks()->count();
        
        // Stats: Tasks
        if ($room) {
            $allTasks = $room->tasks()->get();
            $taskIds = $allTasks->pluck('task_id');
            
            // Ambil submission user untuk tasks di room ini
            $userSubmissions = \App\Models\Submission::whereIn('task_id', $taskIds)
                ->where('user_id', $user->id)
                ->pluck('task_id')
                ->toArray();
            
            // Hitung pending dan completed
            $pendingTasks = $allTasks->filter(function($task) use ($userSubmissions) {
                return !in_array($task->task_id, $userSubmissions);
            })->count();
            
            $completedTasks = count($userSubmissions);
            
            // Ambil tasks yang perlu dikerjakan (belum submit)
            $tasks = $allTasks->filter(function($task) use ($userSubmissions) {
                return !in_array($task->task_id, $userSubmissions);
            })->sortBy('deadline')->take(5);
            
        } else {
            $pendingTasks = 0;
            $completedTasks = 0;
            $tasks = collect();
        }
        
        // Stats: Kehadiran (hanya offline_kantor yang approved, exclude weekend)
        $attendanceDays = $user->logbooks()
            ->where('is_approved', true)
            ->where('keterangan', 'offline_kantor')
            ->get()
            ->filter(function($logbook) {
                // Filter out weekend
                $dayOfWeek = Carbon::parse($logbook->date)->dayOfWeek;
                return !in_array($dayOfWeek, [0, 6]); // 0 = Sunday, 6 = Saturday
            })
            ->count();
        
        // 🔥 Persentase kehadiran dari total hari kerja SELURUH periode magang
        $attendancePercentage = $totalWorkDays > 0 ? round(($attendanceDays / $totalWorkDays) * 100) : 0;
        
        // Stats: Evaluasi (rata-rata nilai dari submissions)
        $averageScore = \App\Models\Submission::where('user_id', $user->id)
            ->whereNotNull('nilai')
            ->avg('nilai');
        $averageScore = $averageScore ? round($averageScore) : 0;
        
        // Logbook hari ini
        $todayLogbook = $user->logbooks()
            ->whereDate('date', today())
            ->first();
        
        // Recent logbooks (3 terakhir)
        $recentLogbooks = $user->logbooks()
            ->with('approver')
            ->orderBy('date', 'desc')
            ->take(3)
            ->get();
        
        // ✅ Announcements (dari tabel pengumuman berdasarkan room yang diikuti)
        $announcements = collect();
        if ($room) {
            $announcements = \App\Models\Pengumuman::where('room_id', $room->room_id)
                ->where('tanggal_kadaluarsa', '>=', now()) // Hanya yang masih berlaku
                ->orderBy('is_penting', 'desc') // Penting duluan
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
        }
        
        $data = [
            "judul" => "Dashboard Peserta",
            "menuDashboard" => "active",
            "user" => $user,
            "room" => $room,
            "peserta" => $peserta,
            "currentWeek" => $currentWeek,
            "totalWeeks" => $totalWeeks,
            "progress" => $progress,
            "totalLogbooks" => $totalLogbooks,
            "totalWorkDays" => $totalWorkDays, // 🔥 Total hari kerja seluruh periode
            "pendingTasks" => $pendingTasks,
            "completedTasks" => $completedTasks,
            "attendanceDays" => $attendanceDays,
            "attendancePercentage" => $attendancePercentage,
            "averageScore" => $averageScore,
            "todayLogbook" => $todayLogbook,
            "tasks" => $tasks,
            "recentLogbooks" => $recentLogbooks,
            "announcements" => $announcements,
        ];

        return view('peserta.dashboard', $data);
    }
}