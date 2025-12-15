<!-- jQuery HARUS PALING PERTAMA -->
    <script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
    
    <!-- Bootstrap 5 JS (GANTI INI) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery Easing & SB Admin -->
    <script src="{{ asset('sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/js/sb-admin-2.min.js') }}"></script>
   
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- DataTables -->
    <script src="{{ asset('sbadmin2/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/js/demo/datatables-demo.js') }}"></script>
    
    <!-- SweetAlert2 -->
    <script src="{{ asset('sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    
    <!-- Script dari halaman child (AJAX kita) -->
    @stack('scripts')

    <script>
    function copyCode(id) {
        const codeElement = document.getElementById(`code-${id}`);
        const codeText = codeElement.innerText.trim();

        navigator.clipboard.writeText(codeText).then(() => {
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