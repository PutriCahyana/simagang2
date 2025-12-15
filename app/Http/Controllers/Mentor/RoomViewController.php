<?php

namespace App\Http\Controllers\Mentor;

use Log;
use App\Models\Room;
use App\Models\Task;
use App\Models\Materi;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RoomViewController extends Controller
{
    public function show($room_id)
    {
        $room = Room::with(['mentor.user', 'materis'])
            ->where('room_id', $room_id)
            ->firstOrFail();
        
        // Pastikan yang akses adalah mentor dari room ini
        if ($room->mentor_id !== Auth::user()->mentor->mentor_id) {
            abort(403, 'Unauthorized');
        }
        
        return view('mentor.room.show', compact('room'));
    }
    
    public function getParticipants($room_id)
    {
        $room = Room::where('room_id', $room_id)->firstOrFail();
        
        if ($room->mentor_id !== Auth::user()->mentor->mentor_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Ambil peserta dari pivot table room_user
        $participants = $room->users()
            ->where('role', 'peserta')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'username' => $user->username,
                    'joined_at' => $user->pivot->created_at ? $user->pivot->created_at->format('d M Y') : '-'
                ];
            });
        
        return response()->json($participants);
    }

    public function showParticipant($room_id, $user_id)
    {
        $room = Room::where('room_id', $room_id)->firstOrFail();
        
        // Pastikan yang akses adalah mentor dari room ini
        if ($room->mentor_id !== Auth::user()->mentor->mentor_id) {
            abort(403, 'Unauthorized');
        }
        
        // Ambil peserta yang ada di room ini
        $peserta = \App\Models\User::whereHas('joinedRooms', function ($query) use ($room_id) {
                $query->where('room.room_id', $room_id);
            })
            ->where('id', $user_id)
            ->where('role', 'peserta')
            ->with(['peserta', 'joinedRooms'])
            ->firstOrFail();
        
        return view('mentor.room.participant', compact('peserta', 'room'));
    }

   public function removeParticipant(Request $request, $room_id, $user_id)
{
    try {
        $room = Room::where('room_id', $room_id)->firstOrFail();
        
        // Pastikan yang akses adalah mentor dari room ini
        $user = Auth::user();
        if (!$user->mentor || $room->mentor_id !== $user->mentor->mentor_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengeluarkan peserta dari room ini'
            ], 403);
        }
        
        // Cek apakah peserta ada di room ini
        $participant = \App\Models\User::whereHas('joinedRooms', function ($query) use ($room_id) {
                $query->where('room.room_id', $room_id);
            })
            ->where('id', $user_id)
            ->where('role', 'peserta')
            ->first();
        
        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta tidak ditemukan di room ini'
            ], 404);
        }
        
        // Hapus peserta dari room (detach dari pivot table)
        $room->users()->detach($user_id);
        
        // Log activity - UBAH INI: ganti 'participant_removed' dengan nilai yang lebih pendek
        Activity::create([
            'user_id' => Auth::id(),
            'room_id' => $room_id,
            'type' => 'removed', // ← UBAH JADI INI (lebih pendek)
            'description' => 'Removed participant: ' . $participant->nama,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Peserta berhasil dikeluarkan dari room',
            'redirect' => route('mentor.room.show', $room_id)
        ], 200);
        
    } catch (\Exception $e) {
        \Log::error('Error removing participant: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}
        
    public function getTasks($room_id)
    {
        $room = Room::where('room_id', $room_id)->firstOrFail();
        
        if ($room->mentor_id !== Auth::user()->mentor->mentor_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $now = now();
        
        // Pisahkan task aktif dan kadaluarsa
        $activeTasks = Task::with(['submissions.user'])
            ->where('room_id', $room_id)
            ->where('deadline', '>=', $now)
            ->orderBy('deadline', 'asc')
            ->get()
            ->map(function($task) {
                return $this->formatTask($task, false);
            });
            
        $expiredTasks = Task::with(['submissions.user'])
            ->where('room_id', $room_id)
            ->where('deadline', '<', $now)
            ->orderBy('deadline', 'desc')
            ->get()
            ->map(function($task) {
                return $this->formatTask($task, true);
            });
        
        return response()->json([
            'active' => $activeTasks,
            'expired' => $expiredTasks
        ]);
    }
    
    private function formatTask($task, $isExpired)
    {
        return [
            'id' => $task->task_id,
            'judul' => $task->judul,
            'deskripsi' => $task->deskripsi,
            'deadline' => $task->deadline->format('d M Y H:i'),
            'deadline_raw' => $task->deadline->toISOString(),
            'file_path' => $task->file_path,
            'file_name' => $task->file_path ? basename($task->file_path) : null,
            'total_submissions' => $task->submissions->count(),
            'is_expired' => $isExpired,
            'submissions' => $task->submissions->map(function($submission) {
                return [
                    'submission_id' => $submission->submission_id,
                    'user_id' => $submission->user->id,
                    'user_nama' => $submission->user->nama,
                    'submitted_at' => $submission->created_at->format('d M Y H:i'),
                    'status' => $submission->status,
                    'nilai' => $submission->nilai,
                    'file_path' => $submission->file_path,
                    'file_name' => $submission->file_path ? basename($submission->file_path) : null,
                    'link' => $submission->link,
                    'catatan' => $submission->catatan,
                    'feedback' => $submission->feedback
                ];
            })
        ];
    }
    
    public function storeTask(Request $request, $room_id)
    {
        $room = Room::where('room_id', $room_id)->firstOrFail();
        
        if ($room->mentor_id !== Auth::user()->mentor->mentor_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'deadline' => 'required|date|after:now',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar|max:10240' // max 10MB
        ]);
        
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('tasks', 'public');
        }
        
        $task = Task::create([
            'room_id' => $room_id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'deadline' => $request->deadline,
            'file_path' => $filePath
        ]);

        Activity::create([
            'user_id' => Auth::id(),
            'room_id' => $room_id,
            'type' => 'task_added',
            'description' => 'New Task: ' . $task->judul,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Task berhasil ditambahkan',
            'task' => $this->formatTask($task, false)
        ]);
    }
    
    public function updateTask(Request $request, $room_id, $task_id)
    {
        $room = Room::where('room_id', $room_id)->firstOrFail();
        
        if ($room->mentor_id !== Auth::user()->mentor->mentor_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $task = Task::where('task_id', $task_id)
            ->where('room_id', $room_id)
            ->firstOrFail();
        
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'deadline' => 'required|date',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar|max:10240'
        ]);
        
        // Handle file upload
        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($task->file_path) {
                Storage::disk('public')->delete($task->file_path);
            }
            $task->file_path = $request->file('file')->store('tasks', 'public');
        }
        
        // Handle file removal
        if ($request->has('remove_file') && $request->remove_file == '1') {
            if ($task->file_path) {
                Storage::disk('public')->delete($task->file_path);
                $task->file_path = null;
            }
        }
        
        $task->judul = $request->judul;
        $task->deskripsi = $request->deskripsi;
        $task->deadline = $request->deadline;
        $task->save();
        
        $isExpired = $task->deadline < now();
        
        return response()->json([
            'success' => true,
            'message' => 'Task berhasil diupdate',
            'task' => $this->formatTask($task, $isExpired)
        ]);
    }
    
    public function deleteTask($room_id, $task_id)
    {
        $room = Room::where('room_id', $room_id)->firstOrFail();
        
        if ($room->mentor_id !== Auth::user()->mentor->mentor_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $task = Task::where('task_id', $task_id)
            ->where('room_id', $room_id)
            ->firstOrFail();
        
        // Hapus file jika ada
        if ($task->file_path) {
            Storage::disk('public')->delete($task->file_path);
        }
        
        $task->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Task berhasil dihapus'
        ]);
    }

    public function downloadSubmission($room_id, $submission_id)
    {
        $room = Room::where('room_id', $room_id)->firstOrFail();
        
        $user = Auth::user();
        if (!$user->mentor) {
            abort(403, 'Data mentor tidak ditemukan');
        }
        
        if ($room->mentor_id !== $user->mentor->mentor_id) {
            abort(403, 'Anda bukan mentor dari room ini');
        }
        
        // GUNAKAN where() EXPLICIT, jangan findOrFail()
        $submission = \App\Models\Submission::where('submission_id', $submission_id)
            ->with('task')
            ->firstOrFail();
        
        \Log::info('Submission found', [
            'submission_id' => $submission->submission_id,
            'task_id' => $submission->task_id,
            'task_room_id' => $submission->task->room_id,
            'requested_room_id' => $room_id,
            'file_path' => $submission->file_path
        ]);
        
        // Cast keduanya ke integer
        if ((int)$submission->task->room_id !== (int)$room_id) {
            abort(403, 'Submission tidak ada di room ini');
        }
        
        if (!$submission->file_path) {
            abort(404, 'File tidak ditemukan. Path: null');
        }
        
        // Cek apakah path sudah include 'public/' atau belum
        $filePath = $submission->file_path;
        if (!str_starts_with($filePath, 'public/')) {
            $filePath = 'public/' . $filePath;
        }
        
        \Log::info('File path check', [
            'original_path' => $submission->file_path,
            'full_path' => $filePath,
            'exists' => Storage::exists($filePath)
        ]);
        
        if (!Storage::exists($filePath)) {
            // Coba tanpa 'public/'
            $alternativePath = str_replace('public/', '', $filePath);
            if (Storage::disk('public')->exists($alternativePath)) {
                return Storage::disk('public')->download($alternativePath);
            }
            
            abort(404, 'File tidak ditemukan di storage. Path: ' . $filePath);
        }
        
        return Storage::download($filePath);
    }
}