@extends('layout.app')

@section('konten')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Profile Saya</h1>
        <p class="text-gray-600 mt-2">Kelola informasi profil dan pengaturan akun Anda</p>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sidebar Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex flex-col items-center">
                    <!-- Foto Profil -->
                    <div class="relative">
                        @if($user->foto_profil)
                            <img src="{{ Storage::url($user->foto_profil) }}" alt="Profile" class="w-32 h-32 rounded-full object-cover mb-4">
                        @else
                            <div class="w-32 h-32 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-4xl font-bold mb-4">
                                {{ strtoupper(substr($user->nama, 0, 2)) }}
                            </div>
                        @endif
                        
                        <!-- Upload Button -->
                        <button onclick="document.getElementById('fotoProfilInput').click()" class="absolute bottom-4 right-0 bg-blue-600 text-white p-2 rounded-full hover:bg-blue-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Form Upload Foto -->
                    <form action="{{ route('mentor.profile.update-foto') }}" method="POST" enctype="multipart/form-data" id="fotoProfilForm">
                        @csrf
                        <input type="file" id="fotoProfilInput" name="foto_profil" class="hidden" accept="image/*" onchange="document.getElementById('fotoProfilForm').submit()">
                    </form>

                    @if($user->foto_profil)
                    <form action="{{ route('mentor.profile.delete-foto') }}" method="POST" class="mt-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 text-xs hover:text-red-700">Hapus Foto</button>
                    </form>
                    @endif

                    <h2 class="text-xl font-bold text-gray-800">{{ $user->nama }}</h2>
                    <p class="text-gray-600 text-sm mt-1">Mentor</p>
                    <div class="mt-4 w-full">
                        <div class="bg-blue-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-600">Status</p>
                            <p class="text-sm font-semibold text-blue-600">Aktif</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Info -->
            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Cepat</h3>
                <div class="space-y-3">
                    <div class="flex items-center text-sm">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-gray-600">Fungsi: {{ $mentor->fungsi ?? '-' }}</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span class="text-gray-600">{{ $mentor->nomor_hp ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Data Diri -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-gray-800">Data Diri</h3>
                    <button onclick="toggleEdit('dataDiri')" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        Edit
                    </button>
                </div>

                <!-- View Mode -->
                <div id="dataDiriView" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Nama Lengkap</label>
                            <p class="mt-1 text-gray-800">{{ $user->nama }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Fungsi</label>
                            <p class="mt-1 text-gray-800">{{ $mentor->fungsi ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Nomor HP</label>
                            <p class="mt-1 text-gray-800">{{ $mentor->nomor_hp ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Edit Mode -->
                <form id="dataDiriEdit" action="{{ route('mentor.profile.update-data-diri') }}" method="POST" class="hidden space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ $user->nama }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fungsi</label>
                            <select name="fungsi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                <option value="Technical" {{ ($mentor->fungsi ?? '') == 'Technical' ? 'selected' : '' }}>Technical</option>
                                <option value="Marketing" {{ ($mentor->fungsi ?? '') == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                <option value="Finance" {{ ($mentor->fungsi ?? '') == 'Finance' ? 'selected' : '' }}>Finance</option>
                                <option value="Human Resources" {{ ($mentor->fungsi ?? '') == 'Human Resources' ? 'selected' : '' }}>Human Resources</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor HP</label>
                            <input type="text" name="nomor_hp" value="{{ $mentor->nomor_hp ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                            Simpan Perubahan
                        </button>
                        <button type="button" onclick="toggleEdit('dataDiri')" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>

            <!-- Account Settings -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-6">Pengaturan Akun</h3>

                <!-- Change Username -->
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h4 class="font-medium text-gray-800">Username</h4>
                            <p class="text-sm text-gray-600 mt-1">Username saat ini: <span class="font-medium">{{ $user->username }}</span></p>
                        </div>
                        <button onclick="toggleEdit('username')" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            Ubah
                        </button>
                    </div>

                    <form id="usernameEdit" action="{{ route('mentor.profile.update-username') }}" method="POST" class="hidden space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Username Baru</label>
                            <input type="text" name="username" placeholder="Masukkan username baru" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                                Simpan
                            </button>
                            <button type="button" onclick="toggleEdit('username')" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change Password -->
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h4 class="font-medium text-gray-800">Password</h4>
                            <p class="text-sm text-gray-600 mt-1">Ubah password Anda secara berkala</p>
                        </div>
                        <button onclick="toggleEdit('password')" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            Ubah
                        </button>
                    </div>

                    <form id="passwordEdit" action="{{ route('mentor.profile.update-password') }}" method="POST" class="hidden space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password Lama</label>
                            <input type="password" name="password_lama" placeholder="Masukkan password lama" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                            <input type="password" name="password_baru" placeholder="Masukkan password baru" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                            <input type="password" name="password_baru_confirmation" placeholder="Konfirmasi password baru" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                                Ubah Password
                            </button>
                            <button type="button" onclick="toggleEdit('password')" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleEdit(section) {
    const viewElement = document.getElementById(section + 'View');
    const editElement = document.getElementById(section + 'Edit');
    
    if (viewElement && editElement) {
        viewElement.classList.toggle('hidden');
        editElement.classList.toggle('hidden');
    } else if (editElement) {
        editElement.classList.toggle('hidden');
    }
}
</script>
@endsection