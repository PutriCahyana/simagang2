<?php

namespace App\Http\Controllers\Mentor;

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
                    'user_id' => $submission->user->id,
                    'user_nama' => $submission->user->nama,
                    'submitted_at' => $submission->created_at->format('d M Y H:i'),
                    'status' => $submission->status
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
}