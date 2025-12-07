<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Submission;
use App\Models\Peserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SubmissionController extends Controller
{
    public function submit(Request $request, $task_id)
    {
        $user = Auth::user();
        $peserta = Peserta::where('peserta_id', $user->id)->first();

        if (!$peserta) {
            return response()->json([
                'success' => false,
                'message' => 'Data peserta tidak ditemukan.'
            ], 403);
        }

        $task = Task::with('room')->findOrFail($task_id);

        // Cek akses ke room
        $hasAccess = $task->room->peserta()
            ->where('users.id', $peserta->peserta_id)
            ->exists();

        if (!$hasAccess) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke tugas ini.'
            ], 403);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'submission_type' => 'required|in:file,link',
            'file' => 'required_if:submission_type,file|file|max:51200', // max 50MB
            'link' => 'required_if:submission_type,link|url',
        ], [
            'submission_type.required' => 'Tipe pengumpulan harus dipilih',
            'file.required_if' => 'File harus diupload',
            'file.max' => 'Ukuran file maksimal 50MB',
            'link.required_if' => 'Link harus diisi',
            'link.url' => 'Format link tidak valid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Cek apakah sudah pernah submit
        $existingSubmission = Submission::where('task_id', $task_id)
            ->where('user_id', $user->id)  // Gunakan user_id
            ->first();

        if ($existingSubmission) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah mengumpulkan tugas ini. Hubungi mentor jika ingin mengubah submission.'
            ], 400);
        }

        $filePath = null;
        $link = null;

        if ($request->submission_type === 'file') {
            // Upload file
            $file = $request->file('file');
            $fileName = time() . '_' . $user->id . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('submissions', $fileName, 'public');
        } else {
            $link = $request->link;
        }

        // Tentukan status (pending/late)
        $status = $task->isOverdue() ? 'late' : 'pending';

        // Simpan submission
        $submission = Submission::create([
            'task_id' => $task_id,
            'user_id' => $user->id,  // Gunakan user_id
            'file_path' => $filePath,
            'link' => $link,
            'catatan' => $request->catatan ?? null,
            'status' => $status,
            'nilai' => null, // Akan diisi oleh mentor
            'feedback' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dikumpulkan!',
            'data' => [
                'submission_id' => $submission->submission_id,
                'submitted_at' => $submission->created_at->format('d M Y H:i'),
                'status' => $status
            ]
        ]);
    }

    public function download($submission_id)
{
    $user = Auth::user();

    // EXPLICIT where() instead of findOrFail()
    $submission = Submission::where('submission_id', $submission_id)
        ->with('task.room')
        ->firstOrFail();

    // Cek kepemilikan submission
    if ($submission->user_id !== $user->id) {
        abort(403, 'Anda tidak memiliki akses ke submission ini.');
    }

    if (!$submission->file_path) {
        abort(404, 'File tidak ditemukan.');
    }

    // Cek dengan berbagai kemungkinan path
    $filePath = $submission->file_path;
    
    // Coba dengan 'public/' prefix
    if (Storage::exists('public/' . $filePath)) {
        return Storage::download('public/' . $filePath);
    }
    
    // Coba langsung
    if (Storage::exists($filePath)) {
        return Storage::download($filePath);
    }
    
    // Coba di disk public
    if (Storage::disk('public')->exists($filePath)) {
        return Storage::disk('public')->download($filePath);
    }
    
    // Coba tanpa 'submissions/' prefix
    $fileName = basename($filePath);
    if (Storage::disk('public')->exists('submissions/' . $fileName)) {
        return Storage::disk('public')->download('submissions/' . $fileName);
    }
    
    abort(404, 'File tidak ditemukan di storage. Path: ' . $filePath);
}
}