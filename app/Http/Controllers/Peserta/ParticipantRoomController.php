<?php

namespace App\Http\Controllers\Peserta;
use App\Models\Room;
use App\Models\Task;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ParticipantRoomController extends Controller
{
     public function index()
    {
        $user = Auth::user();
        $rooms = $user->joinedRooms; // ambil semua room yang diikuti user

         $activities = Activity::whereIn('room_id', $rooms->pluck('id'))
            ->with(['user', 'room'])
            ->latest('created_at')
            ->limit(5)
            ->get();
            
        $data = [
            'judul' => 'My Room',
            'rooms' => $rooms,
            'activities' => $activities,
        ];

        return view('peserta.room.roomlist', $data);
    }

      // Ambil semua task dari room yang diikuti peserta
    public function getUpcomingTasks()
    {
        $user = Auth::user();
        $roomIds = $user->joinedRooms->pluck('id');
        
        // Ambil SEMUA task (tidak filter by deadline dulu)
        $tasks = Task::whereIn('room_id', $roomIds)
            ->select('task_id', 'room_id', 'judul', 'deadline')
            ->get();
        
        // Format untuk kalender - group by tanggal
        $tasksByDate = [];
        foreach ($tasks as $task) {
            $date = $task->deadline->format('Y-m-d');
            if (!isset($tasksByDate[$date])) {
                $tasksByDate[$date] = [];
            }
            $tasksByDate[$date][] = [
                'id' => $task->task_id,
                'judul' => $task->judul,
                'deadline' => $task->deadline->format('d M Y H:i'),
                'is_expired' => $task->deadline < now() // tambah flag
            ];
        }
        
        return response()->json($tasksByDate);
    }
    
    // proses join room pakai kode
    public function join(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:room,code',
        ], [
            'code.exists' => 'Kode room tidak ditemukan.',
        ]);

        $room = Room::where('code', $request->code)->first();

        // cek apakah user sudah join sebelumnya
        if ($room->users()->where('user_id', Auth::id())->exists()) {
            return back()->with('error', 'Kamu sudah bergabung di room ini!');
        }

        $room->users()->attach(Auth::id());

        Activity::create([
            'user_id' => Auth::id(),
            'room_id' => $room->id,
            'type' => 'user_joined',
            'description' => Auth::user()->nama . ' joined the room',
        ]);


        return redirect()->route('peserta.roomlist')->with('success', 'Berhasil bergabung ke room!');
    }
}
