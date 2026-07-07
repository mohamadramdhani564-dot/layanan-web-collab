<?php
session_start();
if(!isset($_SESSION['login'])){
  header("Location: ../login.php");
}
?>

<?php
include '../config/koneksi.php';
?>

<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<!-- SWEETALERT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.print-only {
  display: none;
}
@media print {
  .no-print { display: none !important; }
  .print-only { display: block !important; }
}
</style>

<div class="content-wrapper p-4">

  <h3 class="no-print">Data Pelanggan</h3>

  <!-- FORM TAMBAH -->
  <div class="card no-print">
    <div class="card-header bg-primary text-white">
      Tambah Pelanggan
    </div>
    <div class="card-body">
      <form method="POST">
        <div class="row">
          <div class="col-md-4">
            <input type="text" name="nama" class="form-control" placeholder="Nama pelanggan..." required>
          </div>
          <div class="col-md-4">
            <input type="text" name="no_hp" class="form-control" placeholder="No HP..." required>
          </div>
          <div class="col-md-4">
            <input type="text" name="alamat" class="form-control" placeholder="Alamat lengkap..." required>
          </div>
        </div>
        <br>
        <button name="simpan" class="btn btn-success">Simpan</button>
      </form>
    </div>
  </div>

  <br>

  <!-- FILTER -->
  <div class="card no-print">
    <div class="card-header bg-info text-white">
      Pencarian & Laporan
    </div>
    <div class="card-body">
      <form method="GET">
        <div class="row">
          <div class="col-md-4">
            <input type="text" name="nama" class="form-control" placeholder="Cari nama pelanggan..." value="<?= $_GET['nama'] ?? '' ?>">
          </div>
          <div class="col-md-4">
            <button class="btn btn-primary">Cari</button>
            <a href="pelanggan.php" class="btn btn-secondary">Reset</a>
            <button type="button" onclick="window.print()" class="btn btn-danger">Cetak PDF</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <br>

  <!-- PROSES -->
  <?php
  if(isset($_POST['simpan'])){
    $q = mysqli_query($conn, "INSERT INTO pelanggan VALUES(NULL,'$_POST[nama]','$_POST[alamat]','$_POST[no_hp]')");
    if($q){
      echo "<script>
        Swal.fire('Berhasil!', 'Data pelanggan berhasil disimpan', 'success')
        .then(()=> window.location='pelanggan.php');
      </script>";
    } else {
      echo "<script>
        Swal.fire('Gagal!', 'Data gagal disimpan', 'error');
      </script>";
    }
  }

  if(isset($_GET['hapus'])){
    $q = mysqli_query($conn, "DELETE FROM pelanggan WHERE id_pelanggan='$_GET[hapus]'");
    if($q){
      echo "<script>
        Swal.fire('Terhapus!', 'Data pelanggan berhasil dihapus', 'success')
        .then(()=> window.location='pelanggan.php');
      </script>";
    } else {
      echo "<script>
        Swal.fire('Gagal!', 'Data gagal dihapus', 'error');
      </script>";
    }
  }

  if(isset($_POST['update'])){
    $q = mysqli_query($conn, "UPDATE pelanggan SET 
      nama='$_POST[nama]',
      alamat='$_POST[alamat]',
      no_hp='$_POST[no_hp]'
      WHERE id_pelanggan='$_POST[id]'
    ");
    if($q){
      echo "<script>
        Swal.fire('Berhasil!', 'Data pelanggan diupdate', 'success')
        .then(()=> window.location='pelanggan.php');
      </script>";
    } else {
      echo "<script>
        Swal.fire('Gagal!', 'Data gagal diupdate', 'error');
      </script>";
    }
  }

  $where = "WHERE 1=1";
  if(!empty($_GET['nama'])){
    $where .= " AND nama LIKE '%$_GET[nama]%'";
  }

  $query = "SELECT * FROM pelanggan $where";
  ?>

  <!-- KOP PRINT -->
  <div class="print-only text-center mb-3">
    <h4>LAPORAN DATA PELANGGAN</h4>
    <p>Tanggal Cetak: <?= date('d-m-Y') ?></p>
    <hr>
  </div>

  <!-- TABEL -->
  <div class="card">
    <div class="card-header no-print">Daftar Pelanggan</div>
    <div class="card-body">
      <table class="table table-bordered table-striped">
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>No HP</th>
          <th>Alamat</th>
          <th class="no-print">Aksi</th>
        </tr>

        <?php
        $no = 1;
        $data = mysqli_query($conn, $query);
        while($d = mysqli_fetch_array($data)){
        ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= $d['nama'] ?></td>
          <td><?= $d['no_hp'] ?></td>
          <td><?= $d['alamat'] ?></td>
          <td class="no-print">
            <a href="pelanggan.php?edit=<?= $d['id_pelanggan'] ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="javascript:void(0)" onclick="hapusData(<?= $d['id_pelanggan'] ?>)" class="btn btn-danger btn-sm">Hapus</a>
          </td>
        </tr>
        <?php } ?>
      </table>
    </div>
  </div>

  <br>

  <!-- FORM EDIT -->
  <?php
  if(isset($_GET['edit'])){
    $edit = mysqli_query($conn, "SELECT * FROM pelanggan WHERE id_pelanggan='$_GET[edit]'");
    $e = mysqli_fetch_array($edit);
  ?>
  <div class="card no-print">
    <div class="card-header bg-warning">Edit Pelanggan</div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="id" value="<?= $e['id_pelanggan'] ?>">

        <div class="row">
          <div class="col-md-4">
            <input type="text" name="nama" class="form-control" value="<?= $e['nama'] ?>" required>
          </div>
          <div class="col-md-4">
            <input type="text" name="no_hp" class="form-control" value="<?= $e['no_hp'] ?>" required>
          </div>
          <div class="col-md-4">
            <input type="text" name="alamat" class="form-control" value="<?= $e['alamat'] ?>" required>
          </div>
        </div>

        <br>
        <button name="update" class="btn btn-primary">Perbaharui</button>
        <a href="pelanggan.php" class="btn btn-secondary">Batal</a>
      </form>
    </div>
  </div>
  <?php } ?>

</div>

<script>
function hapusData(id){
  Swal.fire({
    title: 'Yakin hapus?',
    text: "Data tidak bisa dikembalikan!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location = "pelanggan.php?hapus=" + id;
    }
  });
}
</script>

<?php include '../partials/footer.php'; ?>