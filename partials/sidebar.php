<?php 
$base = "http://localhost/LayananWeb/"; 
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- SWEETALERT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="<?= $base ?>dashboard.php" class="brand-link text-center">
    <span class="brand-text font-weight-light">Dealer Mobil</span>
  </a>

  <div class="sidebar">
    <nav>
      <ul class="nav nav-pills nav-sidebar flex-column">

        <!-- Dashboard -->
        <li class="nav-item">
          <a href="<?= $base ?>dashboard.php" class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
            <i class="nav-icon fas fa-home"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <!-- Data Mobil -->
        <li class="nav-item">
          <a href="<?= $base ?>pages/mobil.php" class="nav-link <?= ($current_page == 'mobil.php') ? 'active' : '' ?>">
            <i class="nav-icon fas fa-car"></i>
            <p>Data Mobil</p>
          </a>
        </li>

        <!-- Pelanggan -->
        <li class="nav-item">
          <a href="<?= $base ?>pages/pelanggan.php" class="nav-link <?= ($current_page == 'pelanggan.php') ? 'active' : '' ?>">
            <i class="nav-icon fas fa-users"></i>
            <p>Pelanggan</p>
          </a>
        </li>

        <!-- Penjualan -->
        <li class="nav-item">
          <a href="<?= $base ?>pages/penjualan.php" class="nav-link <?= ($current_page == 'penjualan.php') ? 'active' : '' ?>">
            <i class="nav-icon fas fa-shopping-cart"></i>
            <p>Data Penjualan</p>
          </a>
        </li>

        <!-- Servis -->
        <li class="nav-item">
          <a href="<?= $base ?>pages/servis.php" class="nav-link <?= ($current_page == 'servis.php') ? 'active' : '' ?>">
            <i class="nav-icon fas fa-tools"></i>
            <p>Servis</p>
          </a>
        </li>
        <li class="nav-header">LAPORAN</li>

        <li class="nav-item">
            <a href="<?= $base ?>pages/laporan_penjualan.php"
              class="nav-link <?= ($current_page == 'laporan_penjualan.php') ? 'active' : '' ?>">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>Laporan Penjualan</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="<?= $base ?>pages/laporan_service.php"
              class="nav-link <?= ($current_page == 'laporan_service.php') ? 'active' : '' ?>">
                <i class="nav-icon fas fa-tools"></i>
                <p>Laporan Service</p>
            </a>
        </li>

        <!-- GARIS PEMISAH -->
        <li class="nav-header">AKUN</li>
        <li class="nav-item">
          <a href="<?= $base ?>pages/akun.php" class="nav-link <?= ($current_page == 'akun.php') ? 'active' : '' ?>">
            <i class="nav-icon fas fa-user-friends"></i>
            <p>Kelola Akun</p>
          </a>
        </li>
        <!-- LOGOUT (SWEETALERT) -->
        <li class="nav-item">
          <a href="javascript:void(0)" onclick="logoutConfirm()" class="nav-link">
            <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
            <p class="text-danger">Logout</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>

<!-- SCRIPT LOGOUT -->
<script>
function logoutConfirm(){
  Swal.fire({
    title: 'Yakin logout?',
    text: 'Kamu harus login lagi untuk masuk',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, Logout!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location = "<?= $base ?>logout.php";
    }
  });
}
</script>