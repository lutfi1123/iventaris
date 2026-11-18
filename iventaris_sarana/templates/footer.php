        </div>
        <!-- /.container-fluid -->

        <!-- Footer -->
        <footer class="mt-auto py-3 bg-white border-top text-center text-muted no-print" style="font-size: 0.85rem;">
            <div class="container-fluid">
                <span>&copy; <?= date('Y') ?> Aplikasi Inventaris Sarana dan Prasarana Sekolah SMK | Uji Kompetensi Keahlian (UKK) Rekayasa Perangkat Lunak</span>
            </div>
        </footer>
    </div>
    <!-- /#page-content-wrapper -->
</div>
<!-- /#wrapper -->

<!-- Local Bootstrap 5 Bundle JS -->
<script src="assets/js/bootstrap.bundle.min.js"></script>

<script>
    // Sidebar Toggle for Mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('sidebar-wrapper').classList.toggle('toggled');
        });
    }

    // Auto dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
</body>
</html>
