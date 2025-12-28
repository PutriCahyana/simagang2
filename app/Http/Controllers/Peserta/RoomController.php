<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Peserta;
use App\Models\Task;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    public function show($room_id)
    {
        $user = Auth::user();
        $peserta = Peserta::where('peserta_id', $user->id)->first();

        if (!$peserta) {
            abort(403, 'Data peserta tidak ditemukan.');
        }

        $room = Room::with(['mentor.user', 'tasks.submissions' => function($query) use ($user) {
            $query->where('user_id', $user->id);
        }, 'materis'])->findOrFail($room_id);

        $hasAccess = $room->peserta()
            ->where('users.id', $peserta->peserta_id)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke room ini. Silakan join room terlebih dahulu.');
        }

        $tasks = $room->tasks()->with(['submissions' => function($query) use ($user) {
            $query->where('user_id', $user->id);
        }])->orderBy('deadline', 'asc')->get();

        $tugasList = $tasks->map(function($task) use ($user) {
            $submission = $task->submissions->first();
            
            $status = 'pending';
            if ($submission) {
                $status = 'selesai';
            } elseif ($task->isOverdue()) {
                $status = 'terlambat';
            }

            return [
                'id' => $task->task_id,
                'judul' => $task->judul,
                'deskripsi' => $task->deskripsi,
                'deadline' => $task->deadline->format('Y-m-d'),
                'status' => $status,
                'submitted_at' => $submission ? $submission->created_at->format('Y-m-d H:i:s') : null,
                'grade' => $submission ? $submission->nilai : null,
                'submission' => $submission
            ];
        });

        // ✅ HITUNG RATA-RATA NILAI
        $submissionsWithGrades = $tugasList->filter(function($task) {
            return $task['grade'] !== null;
        });
        
        $averageGrade = $submissionsWithGrades->count() > 0 
            ? round($submissionsWithGrades->avg('grade'), 1) 
            : null;

        // ✅ HITUNG TOTAL JAM BELAJAR (estimasi dari materi)
        $totalJamBelajar = $room->materis()->count() * 0.5; // Asumsi 30 menit = 0.5 jam per materi

        $materiList = $room->materis()->orderBy('created_at', 'desc')->get()->map(function($materi) {
            $tipe = 'artikel';
            if (filter_var($materi->konten, FILTER_VALIDATE_URL)) {
                if (strpos($materi->konten, 'youtube.com') !== false || strpos($materi->konten, 'youtu.be') !== false) {
                    $tipe = 'video';
                } else {
                    $tipe = 'link';
                }
            } else {
                $extension = pathinfo($materi->konten, PATHINFO_EXTENSION);
                if (in_array(strtolower($extension), ['mp4', 'avi', 'mov', 'mkv'])) {
                    $tipe = 'video';
                } elseif (in_array(strtolower($extension), ['pdf'])) {
                    $tipe = 'pdf';
                }
            }

            return [
                'id' => $materi->materi_id,
                'judul' => $materi->judul,
                'deskripsi' => $materi->deskripsi,
                'tipe' => $tipe,
                'durasi' => '30 menit',
                'url' => route('peserta.materials.view', $materi->materi_id)
            ];
        });

        $pengumumanList = \App\Models\Pengumuman::where('room_id', $room_id)
            ->aktif()
            ->orderByDesc('is_penting')
            ->latest()
            ->get();

        return view('peserta.room.show', compact(
            'room', 
            'tugasList', 
            'materiList', 
            'pengumumanList',
            'averageGrade',
            'totalJamBelajar'
        ));
    }

    public function getTaskDetail($task_id)
    {
        $user = Auth::user();
        $peserta = Peserta::where('peserta_id', $user->id)->first();

        $task = Task::with(['room', 'submissions' => function($query) use ($user) {
            $query->where('user_id', $user->id);
        }])->findOrFail($task_id);

        $hasAccess = $task->room->peserta()
            ->where('users.id', $peserta->peserta_id)
            ->exists();

        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $submission = $task->submissions->first();
        
        $status = 'pending';
        if ($submission) {
            $status = 'selesai';
        } elseif ($task->isOverdue()) {
            $status = 'terlambat';
        }

        return response()->json([
            'task_id' => $task->task_id,
            'judul' => $task->judul,
            'deskripsi' => $task->deskripsi,
            'deadline' => $task->deadline->format('Y-m-d'),
            'status' => $status,
            'task_file_path' => $task->file_path,
            'task_file_url' => $task->file_path ? asset('storage/' . $task->file_path) : null,
            'task_file_name' => $task->file_path ? basename($task->file_path) : null,
            'submitted_at' => $submission ? $submission->created_at->format('Y-m-d H:i:s') : null,
            'grade' => $submission ? $submission->nilai : null,
            'submission_file' => $submission ? $submission->file_path : null,
            'submission_link' => $submission ? $submission->link : null,
        ]);
    }
}