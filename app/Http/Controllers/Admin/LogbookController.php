<?php

namespace App\Http\Controllers\Admin;

use App\Models\Room;
use App\Models\User;
use App\Models\Logbook;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogbookController extends Controller
{
    /**
     * Display semua logbook dengan filter
     */
    public function index(Request $request)
    {
        $query = Logbook::with(['user', 'room', 'approver']);

        // Filter by room
        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        // Filter by peserta
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status == 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status == 'pending') {
                $query->where('is_approved', false);
            }
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $logbooks = $query->orderBy('date', 'desc')->paginate(20);

        // Data untuk filter dropdown
        $rooms = Room::all();
        $pesertas = User::where('role', 'peserta')->get();

        return view('admin.logbook.index', compact('logbooks', 'rooms', 'pesertas'));
    }
}