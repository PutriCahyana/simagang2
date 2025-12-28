        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="sidebar-brand-text mx-3">SIMAGANG</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item {{ $menuDashboard ?? '' }}">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

                <!-- Menu Admin -->
                    <!-- Heading -->
                <div class="sidebar-heading">
                    MENU ADMIN
                </div>
                <!-- Nav Item - Mentor -->
                <li class="nav-item {{ $menuAdminUser ?? '' }}">
                    <a class="nav-link" href="{{ route('user') }}">
                        <i class="fas fa-fw fa-user"></i>
                        <span>Mentor</span></a>
                </li>

                <!-- Nav Item - Materi -->
                <li class="nav-item {{ $menuAdminMateri ?? '' }}">
                    <a class="nav-link" href="{{ route('materi') }}">
                        <i class="fas fa-fw fa-table"></i>
                        <span>Materi</span></a>
                </li>
    
                <!-- Nav Item - room -->
                <li class="nav-item {{ $menuAdminRoom ?? '' }}">
                    <a class="nav-link" href="{{ route('room') }}">
                        <i class="fas fa-fw fa-table"></i>
                        <span>Room</span></a>
                </li>
    
                <!-- Nav Item - peserta -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.peserta.index') }}">
                        <i class="fas fa-fw fa-table"></i>
                        <span>Peserta</span></a>
                </li>

                <!-- Nav Item - sertifikat -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.sertifikat.index') }}">
                        <i class="fas fa-fw fa-table"></i>
                        <span>Sertifikat</span></a>
                </li>


            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->
