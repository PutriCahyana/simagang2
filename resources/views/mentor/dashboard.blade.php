@extends('layout/app')

@section('konten')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link href="{{ asset('sbadmin2/css/dashboard.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="container-fluid">
        <!-- Mentor Header -->
        <div class="mentor-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-chalkboard-teacher mr-2"></i>Dashboard Mentor</h1>
                    <p>Selamat datang, <strong>{{ $mentor->nama }}</strong></p>
                </div>
                <div class="col-md-4 text-right">
                    <div class="stats-card d-inline-block">
                        <div class="stats-label">Total Peserta Anda</div>
                        <div class="stats-number">{{ $totalPeserta }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Cards -->
        <h5 class="section-title">Room yang Anda Bimbing</h5>
        <div class="row mb-4">
            @foreach($rooms as $room)
            @php
                $roomPesertaCount = $room->peserta()
                    ->whereHas('peserta', function($q) {
                        $q->where('periode_end', '>=', now());
                    })
                    ->count();
                
                $roomPeriodeCount = $room->peserta()
                    ->with('peserta')
                    ->get()
                    ->filter(function($user) {
                        return $user->peserta && $user->peserta->periode_end >= now();
                    })
                    ->groupBy(function($user) {
                        return \Carbon\Carbon::parse($user->peserta->periode_start)->format('F') . ' - ' . 
                               \Carbon\Carbon::parse($user->peserta->periode_end)->format('F');
                    })
                    ->count();
            @endphp
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card room-card h-100 py-3" onclick="showRoomDetailById({{ $room->room_id }})" style="cursor: pointer;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ $room->nama_room }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $roomPesertaCount }} Peserta Aktif</div>
                                <div class="text-xs text-muted mt-2">
                                    <i class="fas fa-user-clock mr-1"></i>{{ $roomPeriodeCount }} Periode Berjalan
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-laptop-code fa-3x text-primary" style="opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Countdown Period Row -->
        <!-- Ganti bagian Countdown Period Row dengan ini: -->

        <h5 class="section-title">Sisa Waktu Magang Per Periode di Semua Room Anda</h5>
        <p class="text-muted small mb-3">Menampilkan periode yang sedang berjalan. Klik untuk melihat detail peserta</p>
        <div class="row mb-4">
            @foreach($periodeData as $periode)
            @php
                // Hitung progress untuk ring (total 327 adalah circumference circle dengan radius 52)
                $totalDays = 180; // 6 bulan = 180 hari
                $remainingDays = $periode['sisaHari'];
                $percentage = ($remainingDays / $totalDays) * 100;
                $circumference = 2 * 3.14159 * 52; // 2πr
                $offset = $circumference - (($percentage / 100) * $circumference);
            @endphp
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card countdown-card h-100" onclick="showPeriodDetail('{{ $periode['periode'] }}', {{ $periode['sisaHari'] }})" style="cursor: pointer;">
                    <div class="card-body text-center py-4">
                        <div class="countdown-circle">
                            <svg viewBox="0 0 120 120">
                                <circle class="bg" cx="60" cy="60" r="52"></circle>
                                <circle class="progress" 
                                        cx="60" 
                                        cy="60" 
                                        r="52" 
                                        stroke-dasharray="{{ $circumference }}" 
                                        stroke-dashoffset="{{ $offset }}"
                                        style="transition: stroke-dashoffset 1s ease;"></circle>
                            </svg>
                            <div class="countdown-text">{{ $periode['sisaHari'] }}</div>
                        </div>
                        <h5 class="mb-1">Days Left</h5>
                        <p class="mb-1">Periode {{ $periode['periode'] }}</p>
                        <small class="badge badge-light mt-2">{{ $periode['jumlah_peserta'] }} Peserta</small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- Active Participants Chart -->
            <div class="col-xl-6 col-lg-6 mb-4">
                <div class="chart-container">
                    <h6 class="m-0 font-weight-bold text-primary mb-3">
                        <i class="fas fa-user-check mr-2"></i>Peserta Aktif Berdasarkan Institut
                    </h6>
                    <p class="text-muted small mb-3">Klik segment untuk melihat detail peserta dari institut tersebut</p>
                    <canvas id="activeChart"></canvas>
                </div>
            </div>

            <!-- Completed Participants Chart -->
            <div class="col-xl-6 col-lg-6 mb-4">
                <div class="chart-container">
                    <h6 class="m-0 font-weight-bold text-success mb-3">
                        <i class="fas fa-user-graduate mr-2"></i>Peserta Selesai Berdasarkan Institut
                    </h6>
                    <p class="text-muted small mb-3">Klik segment untuk melihat detail peserta yang sudah selesai</p>
                    <canvas id="completedChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row">
            <div class="col-12">
                <div class="chart-container">
                    <h6 class="m-0 font-weight-bold text-info mb-3">
                        <i class="fas fa-chart-bar mr-2"></i>Statistik Singkat
                    </h6>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center p-3">
                                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                <h4 class="mb-0">{{ $stats['peserta_aktif'] }}</h4>
                                <small class="text-muted">Peserta Aktif</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3">
                                <i class="fas fa-user-check fa-2x text-success mb-2"></i>
                                <h4 class="mb-0">{{ $stats['peserta_selesai'] }}</h4>
                                <small class="text-muted">Peserta Selesai</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3">
                                <i class="fas fa-calendar-alt fa-2x text-warning mb-2"></i>
                                <h4 class="mb-0">{{ $stats['periode_berjalan'] }}</h4>
                                <small class="text-muted">Periode Berjalan</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3">
                                <i class="fas fa-university fa-2x text-info mb-2"></i>
                                <h4 class="mb-0">{{ $stats['institut_berbeda'] }}</h4>
                                <small class="text-muted">Institut Berbeda</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Room Details -->
    <div class="modal fade" id="roomModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-door-open mr-2"></i><span id="roomModalTitle">Daftar Peserta</span>
                    </h5>
                    <button type="button" class="close text-white" data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Institut</th>
                                    <th>Periode</th>
                                    <th>Sisa Hari</th>
                                    <th>Status</th>
                                    <th>Room</th>
                                </tr>
                            </thead>
                            <tbody id="roomTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Period Details -->
    <div class="modal fade" id="periodModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="modal-title" id="periodModalTitle">
                        <i class="fas fa-calendar-alt mr-2"></i>Peserta Periode
                    </h5>
                    <button type="button" class="close text-white" data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Institut</th>
                                    <th>Sisa Hari</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="periodTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Institut Details -->
    <div class="modal fade" id="institutModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="institutModalTitle">
                        <i class="fas fa-university mr-2"></i>Peserta dari Institut
                    </h5>
                    <button type="button" class="close text-white" data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Periode</th>
                                    <th>Sisa Hari / Selesai</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="institutTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
// Setup CSRF token untuk AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// ========== FUNGSI MODAL ==========

function showRoomDetail() {
    $.get('/mentor/room-detail', function(data) {
        $('#roomModalTitle').text('Daftar Semua Peserta Aktif');
        const tbody = $('#roomTableBody');
        tbody.empty();
        
        if (data.length === 0) {
            tbody.append('<tr><td colspan="7" class="text-center">Tidak ada peserta aktif</td></tr>');
        } else {
            data.forEach((item, index) => {
                tbody.append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td><a href="/mentor/home/peserta/${item.id}" class="text-primary font-weight-bold">${item.nama}</a></td>
                        <td>${item.institut}</td>
                        <td>${item.periode}</td>
                        <td><span class="badge badge-warning">${item.sisaHari} hari</span></td>
                        <td><span class="badge badge-success">${item.status}</span></td>
                        <td>${item.room}</td>
                    </tr>
                `);
            });
        }
        
        $('#roomModal').modal('show');
    }).fail(function(xhr, status, error) {
        console.error('Error:', error);
        alert('Gagal memuat data peserta. Silakan coba lagi.');
    });
}

function showRoomDetailById(roomId) {
    console.log('Loading room detail for ID:', roomId);
    
    $.get(`/mentor/room/${roomId}/detail`, function(response) {
        $('#roomModalTitle').text(`Daftar Peserta ${response.room}`);
        const tbody = $('#roomTableBody');
        tbody.empty();
        
        if (response.peserta.length === 0) {
            tbody.append('<tr><td colspan="7" class="text-center">Tidak ada peserta aktif di room ini</td></tr>');
        } else {
            response.peserta.forEach((item, index) => {
                tbody.append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td><a href="/mentor/home/peserta/${item.id}" class="text-primary font-weight-bold">${item.nama}</a></td>
                        <td>${item.institut}</td>
                        <td>${item.periode}</td>
                        <td><span class="badge badge-warning">${item.sisaHari} hari</span></td>
                        <td><span class="badge badge-success">${item.status}</span></td>
                        <td>${response.room}</td>
                    </tr>
                `);
            });
        }
        
        $('#roomModal').modal('show');
    }).fail(function(xhr, status, error) {
        console.error('Error:', error);
        console.error('Response:', xhr.responseText);
        alert('Gagal memuat data peserta. Error: ' + xhr.status);
    });
}

function showPeriodDetail(periode, days) {
    $('#periodModalTitle').html(`<i class="fas fa-calendar-alt mr-2"></i>Peserta Periode ${periode} <span class="badge badge-light ml-2">${days} hari tersisa</span>`);
    
    $.get(`/mentor/period/${encodeURIComponent(periode)}`, function(data) {
        const tbody = $('#periodTableBody');
        tbody.empty();
        
        if (data.length === 0) {
            tbody.append('<tr><td colspan="5" class="text-center">Tidak ada peserta di periode ini</td></tr>');
        } else {
            data.forEach((item, index) => {
                tbody.append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td><a href="/mentor/home/peserta/${item.id}" class="text-primary font-weight-bold">${item.nama}</a></td>
                        <td>${item.institut}</td>
                        <td><span class="badge badge-info">${item.sisaHari} hari</span></td>
                        <td><span class="badge badge-success">${item.status}</span></td>
                    </tr>
                `);
            });
        }
        
        $('#periodModal').modal('show');
    }).fail(function(xhr, status, error) {
        console.error('Error:', error);
        alert('Gagal memuat data periode. Silakan coba lagi.');
    });
}

function showInstitutDetail(institut, type) {
    $('#institutModalTitle').html(`<i class="fas fa-university mr-2"></i>Peserta dari ${institut}`);
    
    $.get(`/mentor/institut/${encodeURIComponent(institut)}/${type}`, function(data) {
        const tbody = $('#institutTableBody');
        tbody.empty();
        
        if (data.length === 0) {
            tbody.append('<tr><td colspan="5" class="text-center">Tidak ada peserta dari institut ini</td></tr>');
        } else {
            data.forEach((item, index) => {
                const statusBadge = item.status === 'Aktif' ? 'badge-success' : 'badge-secondary';
                const sisaInfo = item.status === 'Aktif' ? 
                    `<span class="badge badge-warning">${item.sisaHari} hari</span>` : 
                    `<span class="badge badge-secondary">${item.selesai}</span>`;
                
                tbody.append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td><a href="/mentor/home/peserta/${item.id}" class="text-primary font-weight-bold">${item.nama}</a></td>
                        <td>${item.periode}</td>
                        <td>${sisaInfo}</td>
                        <td><span class="badge ${statusBadge}">${item.status}</span></td>
                    </tr>
                `);
            });
        }
        
        $('#institutModal').modal('show');
    }).fail(function(xhr, status, error) {
        console.error('Error:', error);
        alert('Gagal memuat data institut. Silakan coba lagi.');
    });
}

// ========== CHART CONFIGURATION ==========

// Data untuk chart dari backend
const institutActiveData = @json($institutActive);
const institutCompletedData = @json($institutCompleted);

// Warna gradient yang cantik
const blueGradient = {
    start: 'rgba(96, 165, 250, 0.8)',
    end: 'rgba(59, 130, 246, 1)',
    hover: 'rgba(37, 99, 235, 0.9)'
};

const greenGradient = {
    start: 'rgba(52, 211, 153, 0.8)',
    end: 'rgba(16, 185, 129, 1)',
    hover: 'rgba(5, 150, 105, 0.9)'
};

// Chart.js default config
Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif";
Chart.defaults.font.size = 12;
Chart.defaults.color = '#64748b';

// Active Participants Chart
const activeCtx = document.getElementById('activeChart').getContext('2d');

// Create gradient
const activeGradientBg = activeCtx.createLinearGradient(0, 0, 0, 250);
activeGradientBg.addColorStop(0, blueGradient.start);
activeGradientBg.addColorStop(1, blueGradient.end);

const activeChart = new Chart(activeCtx, {
    type: 'bar',
    data: {
        labels: institutActiveData.map(item => item.institut),
        datasets: [{
            label: 'Peserta Aktif',
            data: institutActiveData.map(item => item.total),
            backgroundColor: activeGradientBg,
            borderColor: 'transparent',
            borderWidth: 0,
            borderRadius: 8,
            barThickness: 50,
            maxBarThickness: 60,
            hoverBackgroundColor: blueGradient.hover
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        onClick: (e, activeEls) => {
            if (activeEls.length > 0) {
                const index = activeEls[0].index;
                const label = activeChart.data.labels[index];
                showInstitutDetail(label, 'active');
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    color: '#94a3b8',
                    font: {
                        size: 11
                    }
                },
                grid: {
                    color: '#f1f5f9',
                    drawBorder: false
                }
            },
            x: {
                ticks: {
                    color: '#64748b',
                    font: {
                        size: 11,
                        weight: 500
                    }
                },
                grid: {
                    display: false,
                    drawBorder: false
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                titleColor: '#fff',
                bodyColor: '#e2e8f0',
                padding: 12,
                borderColor: '#3b82f6',
                borderWidth: 1,
                cornerRadius: 8,
                titleFont: {
                    size: 13,
                    weight: 600
                },
                bodyFont: {
                    size: 12
                },
                callbacks: {
                    title: function(context) {
                        return context[0].label;
                    },
                    label: function(context) {
                        return 'Peserta Aktif: ' + context.parsed.y;
                    },
                    afterLabel: function() {
                        return '👆 Klik untuk detail';
                    }
                }
            }
        },
        animation: {
            duration: 800,
            easing: 'easeOutQuart'
        }
    }
});

// Completed Participants Chart
const completedCtx = document.getElementById('completedChart').getContext('2d');

// Create gradient
const completedGradientBg = completedCtx.createLinearGradient(0, 0, 0, 250);
completedGradientBg.addColorStop(0, greenGradient.start);
completedGradientBg.addColorStop(1, greenGradient.end);

const completedChart = new Chart(completedCtx, {
    type: 'bar',
    data: {
        labels: institutCompletedData.map(item => item.institut),
        datasets: [{
            label: 'Peserta Selesai',
            data: institutCompletedData.map(item => item.total),
            backgroundColor: completedGradientBg,
            borderColor: 'transparent',
            borderWidth: 0,
            borderRadius: 8,
            barThickness: 50,
            maxBarThickness: 60,
            hoverBackgroundColor: greenGradient.hover
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        onClick: (e, activeEls) => {
            if (activeEls.length > 0) {
                const index = activeEls[0].index;
                const label = completedChart.data.labels[index];
                showInstitutDetail(label, 'completed');
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    color: '#94a3b8',
                    font: {
                        size: 11
                    }
                },
                grid: {
                    color: '#f1f5f9',
                    drawBorder: false
                }
            },
            x: {
                ticks: {
                    color: '#64748b',
                    font: {
                        size: 11,
                        weight: 500
                    }
                },
                grid: {
                    display: false,
                    drawBorder: false
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                titleColor: '#fff',
                bodyColor: '#e2e8f0',
                padding: 12,
                borderColor: '#10b981',
                borderWidth: 1,
                cornerRadius: 8,
                titleFont: {
                    size: 13,
                    weight: 600
                },
                bodyFont: {
                    size: 12
                },
                callbacks: {
                    title: function(context) {
                        return context[0].label;
                    },
                    label: function(context) {
                        return 'Peserta Selesai: ' + context.parsed.y;
                    },
                    afterLabel: function() {
                        return '👆 Klik untuk detail';
                    }
                }
            }
        },
        animation: {
            duration: 800,
            easing: 'easeOutQuart'
        }
    }
});
</script>
    @endpush
</body>
</html>
@endsection