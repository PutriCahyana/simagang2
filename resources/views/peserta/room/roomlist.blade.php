@extends('layout/app')

@section('konten')
<div class="container-fluid">
    <div class="row">
        <!-- Main Content - Room List -->
        <div class="col-lg-8 col-md-7 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-book-reader"></i>
                    My Rooms
                </h1>
                <button class="btn btn-primary" data-toggle="modal" data-target="#joinRoomModal">
                    <i class="fas fa-plus mr-2"></i>
                    Join Room
                </button>
            </div>

            <!-- Filter Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <small class="text-muted">Filter by:</small>
                        </div>
                        <div class="col-auto">
                            <select class="form-control form-control-sm">
                                <option>All Rooms</option>
                                <option>Active</option>
                                <option>Archived</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <select class="form-control form-control-sm">
                                <option>Sort by Date</option>
                                <option>Sort by Name</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Room Cards -->
            @forelse ($rooms ?? [] as $room)
            <div class="card room-card shadow-sm mb-3 border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="room-icon">
                                <i class="fas fa-laptop-code"></i>
                            </div>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-1 font-weight-bold">{{ $room->nama_room }}</h5>
                            <p class="card-text text-muted small mb-2">
                                {{ Str::limit($room->deskripsi, 100) ?? 'No description available' }}
                            </p>
                            <div class="d-flex align-items-center text-sm">
                                <span class="text-muted mr-3">
                                    <i class="fas fa-user-tie mr-1"></i>
                                    Created by <strong>{{ $room->mentor->user->nama ?? 'Mentor' }}</strong>
                                </span>
                                <span class="badge badge-soft-primary">
                                    <i class="fas fa-users mr-1"></i>
                                    {{ $room->participants_count ?? 0 }} Members
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('peserta.room.show', $room->room_id) }}" class="btn btn-primary btn-sm rounded-circle btn-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <!-- Empty State -->
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5">
                    <div class="empty-state">
                        <div class="empty-icon mb-3">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <h5 class="text-gray-700 mb-2">Kamu belum join room apapun</h5>
                        <p class="text-muted mb-4">Masukkan kode room untuk bergabung dengan kelas</p>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#joinRoomModal">
                            <i class="fas fa-plus mr-2"></i>
                            Join Room Sekarang
                        </button>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Sidebar - Calendar & Online Users -->
        <div class="col-lg-4 col-md-5">
            <!-- Calendar Widget -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 font-weight-bold">
                            <span id="currentMonth">Nov 2025</span>
                        </h6>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary" onclick="previousMonth()">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="nextMonth()">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="calendar">
                        <div class="calendar-header">
                            <div class="calendar-day-header">Mon</div>
                            <div class="calendar-day-header">Tue</div>
                            <div class="calendar-day-header">Wed</div>
                            <div class="calendar-day-header">Thu</div>
                            <div class="calendar-day-header">Fri</div>
                            <div class="calendar-day-header">Sat</div>
                            <div class="calendar-day-header">Sun</div>
                        </div>
                        <div class="calendar-body" id="calendarBody">
                            <!-- Calendar akan di-generate via JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Task Tooltip -->
            <div class="card shadow-sm border-0" id="taskTooltip" style="display: none;">
                <div class="card-body">
                    <button type="button" class="close" onclick="closeTaskTooltip()" style="position: absolute; right: 10px; top: 10px;">
                        <span>&times;</span>
                    </button>
                    <h6 class="mb-3 font-weight-bold">Tasks on <span id="selectedDate"></span></h6>
                    <div id="taskList"></div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 font-weight-bold">Recent Activity</h6>
                        <a href="#" class="text-primary small">See all</a>
                    </div>
                    <div class="activity-list">
                        @forelse ($activities ?? [] as $activity)
                            <div class="activity-item">
                                <div class="activity-icon bg-{{ $activity->type == 'task_added' ? 'primary' : 'success' }}">
                                    <i class="fas fa-{{ $activity->type == 'task_added' ? 'tasks' : 'check' }}"></i>
                                </div>
                                <div class="activity-content">
                                    <p class="mb-0 small">
                                        {{ $activity->description }}
                                        @if($activity->room)
                                            in <strong>{{ $activity->room->nama_room }}</strong>
                                        @endif
                                    </p>
                                    <span class="text-muted" style="font-size: 11px;">{{ $activity->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-3">No activity yet</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('peserta.room.join')

@push('styles')
<style>
/* Room Card Styles */
.room-card {
    transition: all 0.3s ease;
    border-left: 4px solid #4e73df !important;
}

.room-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.room-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.btn-arrow {
    width: 36px;
    height: 36px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.btn-arrow:hover {
    transform: translateX(5px);
}

.badge-soft-primary {
    background-color: rgba(78, 115, 223, 0.1);
    color: #4e73df;
    font-weight: 500;
}

/* Empty State */
.empty-state .empty-icon {
    font-size: 80px;
    color: #e3e6f0;
}

/* Calendar Styles */
.calendar {
    width: 100%;
}

.calendar-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 5px;
    margin-bottom: 10px;
}

.calendar-day-header {
    text-align: center;
    font-size: 11px;
    font-weight: 600;
    color: #858796;
    padding: 5px 0;
}

.calendar-body {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 5px;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}

.calendar-day:hover {
    background-color: #f8f9fc;
}

.calendar-day.today {
    background-color: #4e73df;
    color: white;
    font-weight: 600;
    border-radius: 50%;
}

.calendar-day.has-task::after {
    content: '';
    position: absolute;
    bottom: 4px;
    width: 4px;
    height: 4px;
    background-color: #f6c23e;
    border-radius: 50%;
}

.calendar-day.today.has-task::after {
    background-color: white;
}

.calendar-day.other-month {
    color: #d1d3e2;
}

.calendar-day.has-task {
    cursor: pointer;
    font-weight: 500;
}

/* Activity List */
.activity-list {
    max-height: 300px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #f1f1f1;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
}

/* Task List */
.task-item {
    padding: 8px 0;
    border-bottom: 1px solid #e3e6f0;
}

.task-item:last-child {
    border-bottom: none;
}

.task-item-title {
    font-weight: 500;
    color: #2e59d9;
    margin-bottom: 3px;
    font-size: 13px;
}

.task-item-time {
    font-size: 11px;
    color: #858796;
}
</style>
@endpush

@push('scripts')
<script>
let tasksData = {}; // Menyimpan task data dari server
const today = new Date();
let currentDate = new Date();

// Fetch tasks saat page load
async function fetchTasks() {
    try {
        console.log('Fetching tasks from: {{ route("peserta.upcoming-tasks") }}');
        const response = await fetch('{{ route("peserta.upcoming-tasks") }}');
        
        if (!response.ok) {
            console.error('Response error:', response.status, response.statusText);
            renderCalendar();
            return;
        }
        
        tasksData = await response.json();
        console.log('Tasks data received:', tasksData);
        renderCalendar();
    } catch (error) {
        console.error('Error fetching tasks:', error);
        renderCalendar();
    }
}

function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    document.getElementById('currentMonth').textContent = `${monthNames[month]} ${year}`;
    
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();
    const adjustedFirstDay = firstDay === 0 ? 6 : firstDay - 1;
    
    const calendarBody = document.getElementById('calendarBody');
    calendarBody.innerHTML = '';

    // Tanggal bulan sebelumnya
    for (let i = adjustedFirstDay - 1; i >= 0; i--) {
        const day = daysInPrevMonth - i;
        const div = document.createElement('div');
        div.className = 'calendar-day other-month';
        div.textContent = day;
        calendarBody.appendChild(div);
    }

    // Tanggal bulan ini
    for (let day = 1; day <= daysInMonth; day++) {
        const div = document.createElement('div');
        div.className = 'calendar-day';
        div.textContent = day;

        // Cek tanggal hari ini (realtime)
        if (day === today.getDate() && 
            month === today.getMonth() && 
            year === today.getFullYear()) {
            div.classList.add('today');
        }

        // Cek apakah ada task di tanggal ini
        const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        if (tasksData[dateString]) {
            div.classList.add('has-task');
            div.addEventListener('click', () => showTasksForDate(dateString, day, month, year));
        }

        calendarBody.appendChild(div);
    }

    // Tambahkan sisa sel untuk tampilan rapi
    const totalCells = calendarBody.children.length;
    const remaining = 35 - totalCells;
    for (let i = 1; i <= remaining; i++) {
        const div = document.createElement('div');
        div.className = 'calendar-day other-month';
        div.textContent = i;
        calendarBody.appendChild(div);
    }
}

function previousMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
}

function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
}

function showTasksForDate(dateString, day, month, year) {
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const tasks = tasksData[dateString] || [];
    
    if (tasks.length === 0) return;

    // Update selected date display
    document.getElementById('selectedDate').textContent = `${day} ${monthNames[month]} ${year}`;
    
    // Build task list HTML
    let taskListHtml = '';
    tasks.forEach(task => {
        taskListHtml += `
            <div class="task-item">
                <div class="task-item-title">${task.judul}</div>
                <div class="task-item-time">
                    <i class="fas fa-clock"></i> ${task.deadline}
                </div>
            </div>
        `;
    });
    
    document.getElementById('taskList').innerHTML = taskListHtml;
    document.getElementById('taskTooltip').style.display = 'block';
}

function closeTaskTooltip() {
    document.getElementById('taskTooltip').style.display = 'none';
}

// Initialize pada page load
document.addEventListener('DOMContentLoaded', fetchTasks);
</script>
@endpush

@endsection