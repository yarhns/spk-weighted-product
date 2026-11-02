        <!-- Footer Admin -->
        <footer style="padding: 20px 28px; border-top: 1px solid var(--slate-200); text-align: center; font-size: 0.85rem; color: var(--slate-500); margin-top: auto;">
            <i class="bi bi-c-circle"></i> <?= date('Y') ?> - <strong>Dear Haens</strong>. All rights reserved.
        </footer>
    </main>
</div><!-- /.app-main -->
</div><!-- /.app-wrapper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.app-sidebar .nav-link').forEach(el => {
        el.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                document.getElementById('appSidebar')?.classList.remove('show');
            }
        });
    });
</script>

<!-- Script SweetAlert2 untuk Konfirmasi Logout -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnLogout = document.getElementById('btnLogout');
        if (btnLogout) {
            btnLogout.addEventListener('click', function(e) {
                e.preventDefault(); // Cegah link langsung jalan

                Swal.fire({
                    title: 'Konfirmasi Logout',
                    text: "Apakah Anda yakin ingin keluar dari aplikasi?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444', // Warna merah
                    cancelButtonColor: '#64748b',  // Warna abu-abu
                    confirmButtonText: '<i class="bi bi-box-arrow-right"></i> Ya, Logout',
                    cancelButtonText: 'Batal',
                    reverseButtons: true // Tombol Batal di kiri, Logout di kanan
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Jika user klik "Ya", arahkan ke logout.php
                        window.location.href = 'logout.php';
                    }
                });
            });
        }
    });
</script>
</body>
</html>