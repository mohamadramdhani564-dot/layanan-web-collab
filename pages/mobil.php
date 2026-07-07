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
  .no-print {
    display: none !important;
  }
  .print-only {
    display: block !important;
  }
}
</style>

<div class="content-wrapper p-4">

  <h3 class="no-print">Data Mobil</h3>

  <!-- FORM TAMBAH -->
  <div class="card no-print">
    <div class="card-header bg-primary text-white">
      Tambah Mobil
    </div>
    <div class="card-body">
      <form method="POST">
        <div class="row">
          <div class="col-md-2"><input type="text" name="merk" class="form-control" placeholder="Merk" required></div>
          <div class="col-md-2"><input type="text" name="tipe" class="form-control" placeholder="Tipe" required></div>
          <div class="col-md-2"><input type="number" name="tahun" class="form-control" placeholder="Tahun" required></div>
          <div class="col-md-2">
            <select name="transmisi" class="form-control" required>
              <option value="">Transmisi</option>
              <option value="Manual">Manual</option>
              <option value="Matic">Matic</option>
            </select>
          </div>
          <div class="col-md-2"><input type="text"
            name="harga"
            class="form-control rupiah"
            placeholder="Harga"
            required></div>
          <div class="col-md-2"><input type="number" name="stok" class="form-control" placeholder="Stok" required></div>
        </div>
        <br>
        <button name="simpan" class="btn btn-success">Simpan</button>
      </form>
    </div>
  </div>

  <br>

  <!-- FILTER -->
  <div class="card no-print">
    <div class="card-header bg-info text-white">Filter & Laporan</div>
    <div class="card-body">
      <form method="GET">
        <div class="row">

          <div class="col-md-3">
            <input type="text" name="merk" class="form-control" placeholder="Cari merk..." value="<?= $_GET['merk'] ?? '' ?>">
          </div>

          <div class="col-md-3">
            <input type="number" name="tahun" class="form-control" placeholder="Cari tahun..." value="<?= $_GET['tahun'] ?? '' ?>">
          </div>

          <div class="col-md-3">
            <select name="transmisi" class="form-control">
              <option value="">-- semua transmisi --</option>
              <option value="Manual" <?= (@$_GET['transmisi']=='Manual')?'selected':'' ?>>Manual</option>
              <option value="Matic" <?= (@$_GET['transmisi']=='Matic')?'selected':'' ?>>Matic</option>
            </select>
          </div>

          <div class="col-md-3">
            <button class="btn btn-primary">Cari</button>
            <a href="mobil.php" class="btn btn-secondary">Reset</a>
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
    mysqli_query($conn, "INSERT INTO mobil VALUES(
      NULL,
      '$_POST[merk]',
      '$_POST[tipe]',
      '".str_replace('.','',$_POST['harga'])."',
      '$_POST[stok]',
      '$_POST[tahun]',
      '$_POST[transmisi]'
    )");

    echo "<script>
      Swal.fire('Berhasil!', 'Data mobil ditambahkan', 'success')
      .then(()=> window.location='mobil.php');
    </script>";
  }

  if(isset($_GET['hapus'])){
    mysqli_query($conn, "DELETE FROM mobil WHERE id_mobil='$_GET[hapus]'");

    echo "<script>
      Swal.fire('Terhapus!', 'Data mobil berhasil dihapus', 'success')
      .then(()=> window.location='mobil.php');
    </script>";
  }

  if(isset($_POST['update'])){
    mysqli_query($conn, "UPDATE mobil SET 
      merk='$_POST[merk]',
      tipe='$_POST[tipe]',
      tahun='$_POST[tahun]',
      transmisi='$_POST[transmisi]',
      harga='".str_replace('.','',$_POST['harga'])."',
      stok='$_POST[stok]'
      WHERE id_mobil='$_POST[id]'
    ");

    echo "<script>
      Swal.fire('Berhasil!', 'Data mobil diupdate', 'success')
      .then(()=> window.location='mobil.php');
    </script>";
  }

  $where = "WHERE 1=1";

  if(!empty($_GET['merk'])){
    $where .= " AND merk LIKE '%$_GET[merk]%'";
  }

  if(!empty($_GET['tahun'])){
    $where .= " AND tahun = '$_GET[tahun]'";
  }

  if(!empty($_GET['transmisi'])){
    $where .= " AND transmisi = '$_GET[transmisi]'";
  }

  $query = "SELECT * FROM mobil $where";
  ?>

  <!-- KOP PRINT -->
  <div class="print-only text-center mb-3">
    <h4>LAPORAN DATA MOBIL</h4>
    <p>Tanggal Cetak: <?= date('d-m-Y') ?></p>
    <hr>
  </div>

  <!-- TABEL -->
  <div class="card">
    <div class="card-header no-print">Daftar Mobil</div>
    <div class="card-body">
      <table class="table table-bordered table-striped">
        <tr>
          <th>No</th>
          <th>Merk</th>
          <th>Tipe</th>
          <th>Tahun</th>
          <th>Transmisi</th>
          <th>Harga</th>
          <th>Stok</th>
          <th class="no-print">Aksi</th>
        </tr>

        <?php
        $no = 1;
        $data = mysqli_query($conn, $query);
        while($d = mysqli_fetch_array($data)){
        ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= $d['merk'] ?></td>
          <td><?= $d['tipe'] ?></td>
          <td><?= $d['tahun'] ?></td>
          <td><?= $d['transmisi'] ?></td>
          <td>Rp <?= number_format($d['harga']) ?></td>
          <td><?= $d['stok'] ?></td>
          <td class="no-print">
            <a href="mobil.php?edit=<?= $d['id_mobil'] ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="javascript:void(0)" onclick="hapusData(<?= $d['id_mobil'] ?>)" class="btn btn-danger btn-sm">Hapus</a>
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
  $edit = mysqli_query($conn, "SELECT * FROM mobil WHERE id_mobil='$_GET[edit]'");
  $e = mysqli_fetch_array($edit);
?>
<div class="card no-print">
  <div class="card-header bg-warning">Edit Mobil</div>
  <div class="card-body">
    <form method="POST">
      <input type="hidden" name="id" value="<?= $e['id_mobil'] ?>">

      <div class="row">
        <div class="col-md-2"><input type="text" name="merk" class="form-control" value="<?= $e['merk'] ?>"></div>
        <div class="col-md-2"><input type="text" name="tipe" class="form-control" value="<?= $e['tipe'] ?>"></div>
        <div class="col-md-2"><input type="number" name="tahun" class="form-control" value="<?= $e['tahun'] ?>"></div>
        <div class="col-md-2">
          <select name="transmisi" class="form-control">
            <option value="Manual" <?= $e['transmisi']=='Manual'?'selected':'' ?>>Manual</option>
            <option value="Matic" <?= $e['transmisi']=='Matic'?'selected':'' ?>>Matic</option>
          </select>
        </div>
        <div class="col-md-2"><input type="text"
                                name="harga"
                                class="form-control rupiah"
                                value="<?= number_format($e['harga'],0,',','.') ?>"></div>
        <div class="col-md-2"><input type="number" name="stok" class="form-control" value="<?= $e['stok'] ?>"></div>
      </div>

      <br>
      <button name="update" class="btn btn-primary">Perbaharui</button>
      <a href="mobil.php" class="btn btn-secondary">Batal</a>
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
      window.location = "mobil.php?hapus=" + id;
    }
  });
}
</script>

<?php include '../partials/footer.php'; ?>