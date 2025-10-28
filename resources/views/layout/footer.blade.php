    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/js/sb-admin-2.min.js') }}"></script>
   
    <!-- Chart.js tambahkan di sini -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- Page level plugins -->
    <script src="{{ asset('sbadmin2/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/js/demo/datatables-demo.js') }}"></script>
    <script src="{{ asset('sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    @stack('scripts')

    

    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> --}}

    <script>
    function copyCode(id) {
        const codeElement = document.getElementById(`code-${id}`);
        const codeText = codeElement.innerText.trim();

        // salin ke clipboard
        navigator.clipboard.writeText(codeText).then(() => {
            // tampilkan notifikasi sweetalert
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Kode berhasil disalin ke clipboard.',
                timer: 1500,
                showConfirmButton: false
            });
        }).catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Tidak dapat menyalin kode.',
            });
        });
    }
    </script>


    @session('success')
    <script>
    Swal.fire({
        title: "Berhasil",
        text: "{{ session('success') }}",
        icon: "success"
    });
    </script>
    @endsession

    @session('error')
    <script>
    Swal.fire({
        title: "Gagal",
        text: "{{ session('error') }}",
        icon: "error"
    });
    </script>
    @endsession

    

</body>

</html>