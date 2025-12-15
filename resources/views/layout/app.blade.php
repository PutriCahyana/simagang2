@include('layout/header')

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        @if(Auth::check())
            @if(Auth::user()->role == 'admin')
                @include('layout.sidebar.sadmin')
            @elseif(Auth::user()->role == 'mentor')
                @include('layout.sidebar.smentor')
            @elseif(Auth::user()->role == 'peserta')
                @include('layout.sidebar.speserta')
            @endif
        @endif

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - User Information -->
                        <!-- Ganti SELURUH bagian dropdown di layout/app.blade.php -->

<li class="nav-item dropdown no-arrow">
    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="mr-2 d-none d-lg-inline text-gray-600">{{ auth()->user()->nama }}</span>
        
        @if(auth()->user()->foto_profil)
            <img class="img-profile rounded-circle" 
                 src="{{ Storage::url(auth()->user()->foto_profil) }}" 
                 alt="{{ auth()->user()->nama }}"
                 style="width: 40px; height: 40px; object-fit: cover;">
        @else
            <div class="img-profile rounded-circle d-inline-flex align-items-center justify-content-center text-white font-weight-bold" 
                 style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                {{ strtoupper(substr(auth()->user()->nama, 0, 2)) }}
            </div>
        @endif
    </a>
    
    <!-- Dropdown - User Information -->
    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
        aria-labelledby="userDropdown">
        
        <!-- Role Badge Header - BUKAN dropdown-item -->
        <div class="dropdown-header text-center py-3">
            <span class="badge badge-success px-3 py-2">{{ strtoupper(auth()->user()->role) }}</span>
        </div>
        
        <div class="dropdown-divider my-0"></div>
        
        <!-- Profile Link -->
        @if(auth()->user()->role == 'admin')
            <a class="dropdown-item" href="#">
                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                Profile
            </a>
        @elseif(auth()->user()->role == 'mentor')
            <a class="dropdown-item" href="{{ route('mentor.profile') }}">
                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                Profile
            </a>
        @elseif(auth()->user()->role == 'peserta')
            <a class="dropdown-item" href="{{ route('peserta.profile.index') }}">
                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                Profile
            </a>
        @else
            <a class="dropdown-item" href="#">
                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                Profile
            </a>
        @endif

        <div class="dropdown-divider"></div>
        
        <!-- Logout Link -->
        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
            Logout
        </a>
    </div>
</li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    @yield('konten')

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2020</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="{{ route('logout') }}">Logout</a>
                </div>
            </div>
        </div>
    </div>

@include('layout/footer')