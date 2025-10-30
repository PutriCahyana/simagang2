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
                <a class="nav-link" href="{{ route('mentor.dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Menu Mentor -->
            <!-- Heading -->
            <div class="sidebar-heading">
                MENU MENTOR
            </div>

            <!-- Nav Item - Materi -->
            <li class="nav-item {{ $menuMentorMateri ?? '' }}">
                <a class="nav-link" href="{{ route('mentor.materi') }}">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Materi</span></a>
            </li>

            <!-- Nav Item - room -->
            <li class="nav-item {{ $menuMentorRoom ?? '' }}">
                <a class="nav-link" href="{{ route('mentor.room') }}">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Room</span></a>
            </li>

            <!-- Nav Item - peserta -->
            <li class="nav-item {{ $menuMentorPeserta ?? '' }}">
                <a href="{{ route('mentor.peserta') }}" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Peserta</span></a>
                </a>
            </li>

            <li class="nav-item {{ $menuMentorPeserta ?? '' }}">
                <a class="nav-link" href="{{ route('mentor.logbook.index') }}">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Logbook</span></a>
            </li>



            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->