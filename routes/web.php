<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MateriController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Mentor\RoomViewController as MentorRoomViewController;
use App\Http\Controllers\Mentor\DashboardController as MentorDashboardController;
use App\Http\Controllers\Mentor\MateriController as MentorMateriController;
use App\Http\Controllers\Mentor\PesertaController as MentorPesertaController;
use App\Http\Controllers\Mentor\RoomController as MentorRoomController;
use App\Http\Controllers\Peserta\PesertaDashboardController;
use App\Http\Controllers\Peserta\ParticipantRoomController as PesertaParticipantRoomController;
use App\Http\Controllers\Peserta\MateriController as PesertaMateriController;
use App\Http\Controllers\Peserta\LogbookController as PesertaLogbookController;
use App\Http\Controllers\Mentor\LogbookController as MentorLogbookController;
use App\Http\Controllers\Mentor\ProfileController as MentorProfileController;
use App\Http\Controllers\Admin\LogbookController as AdminLogbookController;

// ===== PUBLIC ROUTES =====
Route::get('/', function(){
    return view('welcome');
})->name('welcome');

// Register
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'registerProses'])->name('register.proses');

// Login
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'loginProses'])->name('loginProses');

// Logout
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// ===== DASHBOARD (SEMUA ROLE BISA AKSES) =====
// Route::middleware('MidLogin')->group(function(){
    
// });

// ===== ADMIN ROUTES =====
Route::middleware(['MidLogin:admin'])->group(function(){
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // User Management
    Route::get('user', [UserController::class, 'index'])->name('user');
    
    // Materi Management
    Route::get('materi', [MateriController::class, 'index'])->name('materi');
    Route::get('materi/create', [MateriController::class, 'create'])->name('materiCreate');
    Route::post('materi/store', [MateriController::class, 'store'])->name('materiStore');
    
    // Room Management
    Route::get('room', [RoomController::class, 'index'])->name('room');
    Route::get('room/create', [RoomController::class, 'create'])->name('roomCreate');
    Route::post('room/store', [RoomController::class, 'store'])->name('room.store');

    Route::get('/logbook', [AdminLogbookController::class, 'index'])->name('logbook.index');
});

// ===== MENTOR ROUTES =====
Route::middleware(['MidLogin:mentor'])->group(function(){
    //dashboard
    Route::get('mentor/dashboard', [MentorDashboardController::class, 'index'])->name('mentor.dashboard');
    //api endpoints untuk modal
    Route::get('/mentor/period/{periode}', [MentorDashboardController::class, 'getPeriodDetail']);
    Route::get('/mentor/institut/{institut}/{type}', [MentorDashboardController::class, 'getInstitutDetail']);
    Route::get('/mentor/room-detail', [MentorDashboardController::class, 'getRoomDetail']);
    Route::get('/room/{roomId}/detail', [MentorDashboardController::class, 'getRoomDetailById']);

    // Materi Mentor
    Route::get('mentor/materi', [MentorMateriController::class, 'index'])->name('mentor.materi');
    Route::get('mentor/materi/create', [MentorMateriController::class, 'create'])->name('mentor.materiCreate');
    Route::post('mentor/materi/store', [MentorMateriController::class, 'store'])->name('mentor.materiStore');

    // Logbook
    Route::get('mentor/logbook', [MentorLogbookController::class, 'index'])->name('mentor.logbook.index');
    Route::post('mentor/logbook/{id}/approve', [MentorLogbookController::class, 'toggleApproval'])->name('mentor.logbook.approve');
    Route::put('mentor/logbook/{id}/keterangan', [MentorLogbookController::class, 'updateKeterangan'])->name('mentor.logbook.keterangan');
    
    // Profile
    Route::get('/profile', [MentorProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [MentorProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/signature', [MentorProfileController::class, 'deleteSignature'])->name('profile.signature.delete');
    
    // Room Mentor
    Route::get('mentor/room', [MentorRoomController::class, 'index'])->name('mentor.room');
    Route::get('mentor/room/create', [MentorRoomController::class, 'create'])->name('mentor.roomCreate');
    Route::post('mentor/room/store', [MentorRoomController::class, 'store'])->name('mentor.roomStore');

    Route::prefix('mentor')->name('mentor.')->group(function () {
        Route::get('/room/{room_id}', [MentorRoomViewController::class, 'show'])->name('room.show');
        
        // API untuk get data
        Route::get('/room/{room_id}/participants', [MentorRoomViewController::class, 'getParticipants']);
        Route::get('/room/{room_id}/tasks', [MentorRoomViewController::class, 'getTasks']);
        
        // API untuk create task
        Route::post('/room/{room_id}/tasks', [MentorRoomViewController::class, 'storeTask']);

    });
    
    // Peserta Mentor
    // Route::get('mentor/peserta', [MentorPesertaController:class, 'mentorPeserta'])->name('mentor.peserta');
});

// ===== PESERTA ROUTES =====
Route::middleware(['MidLogin:peserta'])->group(function(){
    Route::get('peserta/dashboard', [PesertaDashboardController::class, 'index'])->name('peserta.dashboard');
    Route::get('peserta/roomlist', [PesertaParticipantRoomController::class, 'index'])->name('peserta.roomlist');
    Route::post('peserta/roomlist/join', [PesertaParticipantRoomController::class, 'join'])->name('peserta.roomlist.join');
    // Route::get('peserta/room/join', [PesertaRoomController::class, ''])->name('peserta.roomlist');
    Route::get('peserta/materials', [PesertaMateriController::class, 'index'])->name('peserta.materials');
    Route::get('peserta/materials/{id}', [PesertaMateriController::class, 'view'])->name('peserta.materials.view');
    Route::get('peserta/materials/{id}/download', [PesertaMateriController::class, 'download'])->name('peserta.materials.download');
    Route::get('peserta/materials/{id}/view-pdf', [PesertaMateriController::class, 'viewPdf'])->name('peserta.materials.view-pdf');
    Route::get('peserta/materials/{id}/stream', [PesertaMateriController::class, 'stream'])->name('peserta.materials.stream');

    // Logbook
    Route::get('peserta/logbook', [PesertaLogbookController::class, 'index'])->name('peserta.logbook.index');
    Route::get('peserta/logbook/create', [PesertaLogbookController::class, 'create'])->name('peserta.logbook.create');
    Route::post('peserta/logbook', [PesertaLogbookController::class, 'store'])->name('peserta.logbook.store');
    Route::get('peserta/logbook/{id}/edit', [PesertaLogbookController::class, 'edit'])->name('peserta.logbook.edit');
    Route::put('peserta/logbook/{id}', [PesertaLogbookController::class, 'update'])->name('peserta.logbook.update');
    Route::delete('peserta/logbook/{id}', [PesertaLogbookController::class, 'destroy'])->name('peserta.logbook.destroy');
    
    // Export
    Route::get('peserta/logbook/export/pdf', [PesertaLogbookController::class, 'exportPdf'])->name('peserta.logbook.export.pdf');
    Route::get('peserta/logbook/export/excel', [PesertaLogbookController::class, 'exportExcel'])->name('peserta.logbook.export.excel');


    Route::prefix('peserta')->name('peserta.')->group(function () {

         Route::get('/rooms/{room_id}', [MentorRoomViewController::class, 'show'])
            ->name('mentor.room.show'); 
        
        // API untuk get data
        Route::get('/rooms/{room_id}/participants', [MentorRoomViewController::class, 'getParticipants']);
        Route::get('/rooms/{room_id}/tasks', [MentorRoomViewController::class, 'getTasks']);
        
        // API untuk create task
        Route::post('/rooms/{room_id}/tasks', [MentorRoomViewController::class, 'storeTask']);
    });

 
});

