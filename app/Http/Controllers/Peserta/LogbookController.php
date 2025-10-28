<?php

namespace App\Http\Controllers\Peserta;

use Carbon\Carbon;
use App\Models\Logbook;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;
use App\Exports\LogbookExcelExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class LogbookController extends Controller
{
    /**
     * Tampilkan daftar logbook peserta
     */
    public function index()
    {
        $logbooks = Logbook::where('user_id', auth()->id())
            ->with(['room', 'approver'])
            ->orderBy('date', 'desc')
            ->paginate(15);

        return view('peserta.logbook.index', compact('logbooks'));
    }

    /**
     * Form tambah logbook baru
     */
    public function create()
    {
        $rooms = auth()->user()->rooms()->get();
        return view('peserta.logbook.create', compact('rooms'));
    }

    /**
     * Simpan logbook baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:room,room_id',
            'date' => 'required|date|before_or_equal:today',
            'aktivitas' => 'required|string|max:1000',
            'keterangan' => 'required|in:offline_kantor,sakit,izin,online,alpha',
        ]);

        if (Logbook::isWeekend($request->date)) {
            return back()->withErrors(['date' => 'Tidak boleh mengisi logbook di hari Sabtu/Minggu.']);
        }

        $exists = Logbook::where('user_id', auth()->id())
            ->where('room_id', $request->room_id)
            ->where('date', $request->date)
            ->exists();

        if ($exists) {
            return back()->withErrors(['date' => 'Anda sudah mengisi logbook untuk tanggal ini di room yang dipilih.']);
        }

        $defaultJam = Logbook::getDefaultJam($request->date);

        Logbook::create([
            'user_id' => auth()->id(),
            'room_id' => $request->room_id,
            'date' => $request->date,
            'jam_masuk' => $defaultJam['jam_masuk'],
            'jam_keluar' => $defaultJam['jam_keluar'],
            'aktivitas' => $request->aktivitas,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('peserta.logbook.index')
            ->with('success', 'Logbook berhasil ditambahkan!');
    }

    /**
     * Form edit logbook
     */
    public function edit($id)
    {
        $logbook = Logbook::where('user_id', auth()->id())->findOrFail($id);
        $rooms = auth()->user()->rooms;

        return view('peserta.logbook.edit', compact('logbook', 'rooms'));
    }

    /**
     * Update logbook
     */
    public function update(Request $request, $id)
    {
        $logbook = Logbook::where('user_id', auth()->id())->findOrFail($id);

        if ($logbook->is_approved) {
            return back()->withErrors(['error' => 'Logbook yang sudah di-approve tidak dapat diedit.']);
        }

        $request->validate([
            'room_id' => 'required|exists:room,room_id',
            'date' => 'required|date|before_or_equal:today',
            'aktivitas' => 'required|string|max:1000',
            'keterangan' => 'required|in:offline_kantor,sakit,izin,online,alpha',
        ]);

        if (Logbook::isWeekend($request->date)) {
            return back()->withErrors(['date' => 'Tidak boleh mengisi logbook di hari Sabtu/Minggu.']);
        }

        $exists = Logbook::where('user_id', auth()->id())
            ->where('room_id', $request->room_id)
            ->where('date', $request->date)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['date' => 'Anda sudah mengisi logbook untuk tanggal ini di room yang dipilih.']);
        }

        $defaultJam = Logbook::getDefaultJam($request->date);

        $logbook->update([
            'room_id' => $request->room_id,
            'date' => $request->date,
            'jam_masuk' => $defaultJam['jam_masuk'],
            'jam_keluar' => $defaultJam['jam_keluar'],
            'aktivitas' => $request->aktivitas,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('peserta.logbook.index')
            ->with('success', 'Logbook berhasil diupdate!');
    }

    /**
     * Hapus logbook
     */
    public function destroy($id)
    {
        $logbook = Logbook::where('user_id', auth()->id())->findOrFail($id);

        if ($logbook->is_approved) {
            return back()->withErrors(['error' => 'Logbook yang sudah di-approve tidak dapat dihapus.']);
        }

        $logbook->delete();

        return redirect()->route('peserta.logbook.index')
            ->with('success', 'Logbook berhasil dihapus!');
    }

    /**
     * Export PDF (logbook yang sudah di-approve)
     */
    public function exportPdf()
    {
        $logbooks = Logbook::where('user_id', auth()->id())
            ->where('is_approved', true)
            ->with(['room', 'approver.mentor'])
            ->orderBy('date', 'asc')
            ->get();

        return Pdf::view('peserta.logbook.pdf', [
            'logbooks' => $logbooks,
            'peserta' => auth()->user(),
        ])
        ->format('a4')
        ->margins(10, 10, 10, 10)
        ->name('logbook_' . str_replace(' ', '_', strtolower(auth()->user()->nama)) . '_' . now()->format('Ymd') . '.pdf');
    }

    /**
     * Export Excel (logbook yang sudah di-approve)
     */
    public function exportExcel()
    {
        return Excel::download(
            new LogbookExcelExport(auth()->id()),
            'logbook_' . str_replace(' ', '_', strtolower(auth()->user()->nama)) . '_' . now()->format('Ymd') . '.xlsx'
        );
    }
}
