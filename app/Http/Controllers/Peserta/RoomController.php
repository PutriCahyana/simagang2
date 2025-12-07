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

        // Ambil data room dengan relasi
        $room = Room::with(['mentor.user', 'tasks.submissions' => function($query) use ($user) {
            $query->where('user_id', $user->id);  // Gunakan user_id
        }, 'materis'])->findOrFail($room_id);

        // Cek apakah peserta memiliki akses ke room ini
        $hasAccess = $room->peserta()
            ->where('users.id', $peserta->peserta_id)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke room ini. Silakan join room terlebih dahulu.');
        }

        // Ambil semua tugas dengan status submission peserta
        $tasks = $room->tasks()->with(['submissions' => function($query) use ($user) {
            $query->where('user_id', $user->id);  // Gunakan user_id
        }])->orderBy('deadline', 'asc')->get();

        // Mapping tugas dengan status
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

        // Ambil materi yang berelasi dengan room
        $materiList = $room->materis()->orderBy('created_at', 'desc')->get()->map(function($materi) {
            // Deteksi tipe berdasarkan konten atau file
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
                'durasi' => '30 menit', // Bisa dikustomisasi jika ada field durasi
                'url' => route('peserta.materials.view', $materi->materi_id)
            ];
        });

        return view('peserta.room.show', compact('room', 'tugasList', 'materiList'));
    }

    public function getTaskDetail($task_id)
    {
        $user = Auth::user();
        $peserta = Peserta::where('peserta_id', $user->id)->first();

        $task = Task::with(['room', 'submissions' => function($query) use ($user) {
            $query->where('user_id', $user->id);  // Gunakan user_id
        }])->findOrFail($task_id);

        // Cek akses
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
            'submitted_at' => $submission ? $submission->created_at->format('Y-m-d H:i:s') : null,
            'grade' => $submission ? $submission->nilai : null,
            'submission_file' => $submission ? $submission->file_path : null,
            'submission_link' => $submission ? $submission->link : null,
        ]);
    }
}