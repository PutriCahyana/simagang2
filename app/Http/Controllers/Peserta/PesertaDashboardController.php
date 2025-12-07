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
        $periodeStart = $peserta ? Carbon::parse($peserta->periode_start) : null;
        $periodeEnd = $peserta ? Carbon::parse($peserta->periode_end) : null;
        $currentWeek = $periodeStart ? ceil($periodeStart->diffInWeeks(now()) + 1) : 0;
        $totalWeeks = $periodeStart && $periodeEnd ? ceil($periodeStart->diffInWeeks($periodeEnd)) : 12;
        $progress = $totalWeeks > 0 ? round(($currentWeek / $totalWeeks) * 100) : 0;
        
        // Hitung total hari kerja (exclude weekend) sejak periode start sampai sekarang
        $workDays = 0;
        if ($periodeStart) {
            $start = $periodeStart->copy();
            $end = now();
            
            while ($start->lte($end)) {
                // Skip Saturday (6) dan Sunday (0)
                if (!in_array($start->dayOfWeek, [0, 6])) {
                    $workDays++;
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
        
        $attendancePercentage = $workDays > 0 ? round(($attendanceDays / $workDays) * 100) : 0;
        
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
        
        // Announcements (dari Activity model, tipe announcement di room)
        $announcements = collect();
        if ($room) {
            $announcements = \App\Models\Activity::where('room_id', $room->room_id)
                ->where('type', 'announcement')
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
            "workDays" => $workDays,
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