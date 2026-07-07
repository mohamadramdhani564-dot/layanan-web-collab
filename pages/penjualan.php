<?php
session_start();
if(!isset($_SESSION['login'])){
  header("Location: ../login.php");
}
include '../config/koneksi.php';
?>

<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<!-- SWEETALERT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.print-only { display: none; }

@media print {
  .no-print { display: none !important; }
  .print-only { display: block !important; }
}
</style>

<div class="content-wrapper p-4">

<h3 class="no-print">Transaksi Penjualan</h3>

<!-- FORM INPUT -->
<div class="card no-print">
  <div class="card-header bg-warning">Input Penjualan</div>
  <div class="card-body">
    <form method="POST" enctype="multipart/form-data">
      <div class="row">

        <div class="col-md-3">
          <label>Pelanggan</label>
          <select name="pelanggan" class="form-control" required>
            <option value="">-- pilih pelanggan --</option>
            <?php
            $pel = mysqli_query($conn, "SELECT * FROM pelanggan");
            while($p = mysqli_fetch_array($pel)){
              echo "<option value='$p[id_pelanggan]'>$p[nama]</option>";
            }
            ?>
          </select>
        </div>

        <div class="col-md-3">
          <label>Merk</label>
          <select id="merk" class="form-control" onchange="filterMobil()">
            <option value="">-- pilih merk --</option>
            <?php
            $merk = mysqli_query($conn, "SELECT DISTINCT merk FROM mobil");
            while($m = mysqli_fetch_array($merk)){
              echo "<option value='$m[merk]'>$m[merk]</option>";
            }
            ?>
          </select>
        </div>

        <div class="col-md-3">
          <label>Tipe Mobil</label>
          <select name="mobil" id="mobil" class="form-control" required disabled>
            <option value="">-- pilih merk dulu --</option>
            <?php
            $mob = mysqli_query($conn, "SELECT * FROM mobil");
            while($m = mysqli_fetch_array($mob)){
              echo "<option value='$m[id_mobil]' data-merk='$m[merk]'>
              $m[merk] $m[tipe] ($m[tahun], $m[transmisi])
              </option>";
            }
            ?>
          </select>
        </div>

        <div class="col-md-3">
          <label>Jumlah</label>
          <input type="number" name="jumlah" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label>Upload Nota</label>

          <input type="file"
                name="nota"
                class="form-control"
                accept=".jpg,.jpeg,.png,.pdf">
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
  <div class="card-header bg-info text-white">Filter & Laporan</div>
  <div class="card-body">
    <form method="GET">
      <div class="row">

        <div class="col-md-3">
          <input type="date" name="tanggal" class="form-control" value="<?= $_GET['tanggal'] ?? '' ?>">
        </div>

        <div class="col-md-3">
          <input type="text" name="mobil" class="form-control" placeholder="Cari mobil..." value="<?= $_GET['mobil'] ?? '' ?>">
        </div>

        <div class="col-md-3">
          <input type="number" name="tahun" class="form-control" placeholder="Tahun" value="<?= $_GET['tahun'] ?? '' ?>">
        </div>

        <div class="col-md-3">
          <select name="transmisi" class="form-control">
            <option value="">-- semua transmisi --</option>
            <option value="Manual" <?= (@$_GET['transmisi']=='Manual')?'selected':'' ?>>Manual</option>
            <option value="Matic" <?= (@$_GET['transmisi']=='Matic')?'selected':'' ?>>Matic</option>
          </select>
        </div>

        <div class="col-md-12 mt-2">

            <button class="btn btn-primary">Cari</button>

            <a href="penjualan.php" class="btn btn-secondary">Reset</a>

            <a href="cetak_penjualan.php" target="_blank" class="btn btn-danger">
                Cetak Penjualan
            </a>

        </div>

      </div>
    </form>
  </div>
</div>

<br>

<?php
// SIMPAN
if(isset($_POST['simpan'])){

  $m = mysqli_fetch_array(mysqli_query($conn,
    "SELECT * FROM mobil WHERE id_mobil='$_POST[mobil]'"
  ));

  if($_POST['jumlah'] > $m['stok']){

    echo "<script>
      Swal.fire('Gagal!','Stok tidak cukup!','error');
    </script>";

  } else {

    $total = $m['harga'] * $_POST['jumlah'];

    // =========================
    // UPLOAD NOTA
    // =========================
    $nota = '';

    if($_FILES['nota']['name'] != ''){

      $nama_file = time().'_'.$_FILES['nota']['name'];

      $tmp = $_FILES['nota']['tmp_name'];

      move_uploaded_file(
        $tmp,
        "../uploads/nota/".$nama_file
      );

      $nota = $nama_file;
    }

    // =========================
    // SIMPAN PENJUALAN
    // =========================
    mysqli_query($conn,"
      INSERT INTO penjualan
      VALUES(
        NULL,
        CURDATE(),
        '$_POST[pelanggan]',
        '$total',
        '$nota'
      )
    ");

    $id = mysqli_insert_id($conn);

    mysqli_query($conn,"
      INSERT INTO detail_penjualan
      VALUES(
        NULL,
        '$id',
        '$_POST[mobil]',
        '$_POST[jumlah]'
      )
    ");

    mysqli_query($conn,"
      UPDATE mobil
      SET stok=stok-$_POST[jumlah]
      WHERE id_mobil='$_POST[mobil]'
    ");

    echo "<script>
      Swal.fire(
        'Berhasil!',
        'Transaksi berhasil disimpan',
        'success'
      ).then(()=>{
        window.location='penjualan.php';
      });
    </script>";

  }
}

// UPDATE
if(isset($_POST['update'])){

  $lama = mysqli_fetch_array(mysqli_query($conn,
    "SELECT * FROM detail_penjualan
     WHERE id_penjualan='$_POST[id]'"
  ));

  mysqli_query($conn,
    "UPDATE mobil
     SET stok=stok+$lama[jumlah]
     WHERE id_mobil='$lama[id_mobil]'"
  );

  $m = mysqli_fetch_array(mysqli_query($conn,
    "SELECT * FROM mobil
     WHERE id_mobil='$_POST[mobil]'"
  ));

  if($_POST['jumlah'] > $m['stok']){

    echo "<script>
      Swal.fire('Gagal!','Stok tidak cukup!','error');
    </script>";

  } else {

    $total = $m['harga'] * $_POST['jumlah'];

    // =========================
    // UPLOAD NOTA BARU
    // =========================
    $nota_update = "";

    if($_FILES['nota']['name'] != ''){

      $nama_file = time().'_'.$_FILES['nota']['name'];

      $tmp = $_FILES['nota']['tmp_name'];

      move_uploaded_file(
        $tmp,
        "../uploads/nota/".$nama_file
      );

      $nota_update = ", nota='$nama_file'";
    }

    // =========================
    // UPDATE PENJUALAN
    // =========================
    mysqli_query($conn,"
      UPDATE penjualan SET
      id_pelanggan='$_POST[pelanggan]',
      total='$total'
      $nota_update
      WHERE id_penjualan='$_POST[id]'
    ");

    mysqli_query($conn,"
      UPDATE detail_penjualan SET
      id_mobil='$_POST[mobil]',
      jumlah='$_POST[jumlah]'
      WHERE id_penjualan='$_POST[id]'
    ");

    mysqli_query($conn,"
      UPDATE mobil SET
      stok=stok-$_POST[jumlah]
      WHERE id_mobil='$_POST[mobil]'
    ");

    echo "<script>
      Swal.fire(
        'Berhasil!',
        'Data berhasil diupdate',
        'success'
      ).then(()=>{
        window.location='penjualan.php';
      });
    </script>";

  }
}

// HAPUS
if(isset($_GET['hapus'])){
  $det = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM detail_penjualan WHERE id_penjualan='$_GET[hapus]'"));

  mysqli_query($conn, "UPDATE mobil SET stok=stok+$det[jumlah] WHERE id_mobil='$det[id_mobil]'");
  mysqli_query($conn, "DELETE FROM detail_penjualan WHERE id_penjualan='$_GET[hapus]'");
  mysqli_query($conn, "DELETE FROM penjualan WHERE id_penjualan='$_GET[hapus]'");

  echo "<script>
  Swal.fire('Terhapus!','Data berhasil dihapus','success')
  .then(()=> window.location='penjualan.php');
  </script>";
}

// FILTER
$where="WHERE 1=1";
if(!empty($_GET['tanggal'])) $where.=" AND p.tanggal='$_GET[tanggal]'";
if(!empty($_GET['mobil'])) $where.=" AND (m.merk LIKE '%$_GET[mobil]%' OR m.tipe LIKE '%$_GET[mobil]%')";
if(!empty($_GET['tahun'])) $where.=" AND m.tahun='$_GET[tahun]'";
if(!empty($_GET['transmisi'])) $where.=" AND m.transmisi='$_GET[transmisi]'";
?>
<!-- KOP PRINT -->
  <div class="print-only text-center mb-3">
    <h4>LAPORAN DATA PEBJUALAN</h4>
    <p>Tanggal Cetak: <?= date('d-m-Y') ?></p>
    <hr>
  </div>

<!-- TABEL -->
<div class="card">
<div class="card-header no-print">Data Penjualan</div>
<div class="card-body">
<table class="table table-bordered">
<tr>
<th>No</th><th>Tanggal</th><th>Pelanggan</th><th>Mobil</th>
<th>Tahun</th><th>Transmisi</th><th>Harga</th>
<th>Jumlah</th><th>Total</th>
<th class="no-print">Nota</th>
<th class="no-print">Aksi</th>
</tr>

<?php
$no=1;
$data=mysqli_query($conn,"
SELECT p.*,pl.nama,m.*,dp.jumlah 
FROM penjualan p
JOIN pelanggan pl ON p.id_pelanggan=pl.id_pelanggan
JOIN detail_penjualan dp ON p.id_penjualan=dp.id_penjualan
JOIN mobil m ON dp.id_mobil=m.id_mobil
$where
");

while($d=mysqli_fetch_array($data)){
?>

<tr>
<td><?= $no++ ?></td>
<td><?= $d['tanggal'] ?></td>
<td><?= $d['nama'] ?></td>
<td><?= $d['merk'].' '.$d['tipe'] ?></td>
<td><?= $d['tahun'] ?></td>
<td><?= $d['transmisi'] ?></td>
<td>Rp <?= number_format($d['harga']) ?></td>
<td><?= $d['jumlah'] ?></td>
<td>Rp <?= number_format($d['total']) ?></td>
<td class="no-print">

<?php if($d['nota'] != ''){ ?>

<a href="../uploads/nota/<?= $d['nota'] ?>"
   target="_blank"
   class="btn btn-success btn-sm">
   Lihat Nota
</a>

<?php } else { ?>

<span class="text-danger">Tidak ada</span>

<?php } ?>

</td>
<td class="no-print">
<a href="?edit=<?= $d['id_penjualan'] ?>" class="btn btn-warning btn-sm">Edit</a>
<a href="javascript:void(0)" onclick="hapusData(<?= $d['id_penjualan'] ?>)" class="btn btn-danger btn-sm">Hapus</a>
</td>
</tr>

<?php } ?>
</table>
</div>
</div>

<!-- FORM EDIT -->
<?php
if(isset($_GET['edit'])){
$id=$_GET['edit'];

$e=mysqli_fetch_array(mysqli_query($conn,"
SELECT p.*,dp.id_mobil,dp.jumlah
FROM penjualan p
JOIN detail_penjualan dp ON p.id_penjualan=dp.id_penjualan
WHERE p.id_penjualan='$id'
"));
?>

<div class="card mt-3 no-print">
<div class="card-header bg-info text-white">Edit Transaksi</div>
<div class="card-body">
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?= $id ?>">

<select name="pelanggan" class="form-control mb-2">
<?php
$pel=mysqli_query($conn,"SELECT * FROM pelanggan");
while($p=mysqli_fetch_array($pel)){
$sel=($p['id_pelanggan']==$e['id_pelanggan'])?'selected':'';
echo "<option value='$p[id_pelanggan]' $sel>$p[nama]</option>";
}
?>
</select>

<select name="mobil" class="form-control mb-2">
<?php
$mob=mysqli_query($conn,"SELECT * FROM mobil");
while($m=mysqli_fetch_array($mob)){
$sel=($m['id_mobil']==$e['id_mobil'])?'selected':'';
echo "<option value='$m[id_mobil]' $sel>$m[merk] $m[tipe]</option>";
}
?>
</select>

<input type="number"
       name="jumlah"
       value="<?= $e['jumlah'] ?>"
       class="form-control mb-2">

<label>Upload Nota Baru</label>

<input type="file"
       name="nota"
       class="form-control mb-2"
       accept=".jpg,.jpeg,.png,.pdf">


<br>

<button name="update" class="btn btn-primary">
  Perbaharui
</button>

<a href="penjualan.php" class="btn btn-secondary">Batal</a>

</form>
</div>
</div>

<?php } ?>

</div>

<script>
function filterMobil(){
  var merk = document.getElementById("merk").value;
  var mobil = document.getElementById("mobil");

  mobil.disabled = (merk === "");

  for (var i = 0; i < mobil.options.length; i++) {
    var option = mobil.options[i];
    if(option.value == "") continue;
    option.style.display = (option.getAttribute("data-merk") == merk) ? "block" : "none";
  }

  mobil.value = "";
}

function hapusData(id){
  Swal.fire({
    title: 'Yakin hapus?',
    text: "Data tidak bisa dikembalikan!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location = "penjualan.php?hapus=" + id;
    }
  });
}
</script>

<?php include '../partials/footer.php'; ?>