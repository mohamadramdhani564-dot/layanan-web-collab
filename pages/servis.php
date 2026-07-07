<?php
session_start();

if(!isset($_SESSION['login'])){
  header("Location: ../login.php");
  exit;
}

include '../config/koneksi.php';
?>

<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<!-- SWEETALERT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.print-only{
  display:none;
}

@media print{
  .no-print{
    display:none !important;
  }

  .print-only{
    display:block !important;
  }
}
</style>

<div class="content-wrapper p-4">

<h3 class="no-print">Data Servis Kendaraan</h3>

<!-- FORM TAMBAH -->
<div class="card no-print">

  <div class="card-header bg-info text-white">
    Input Servis
  </div>

  <div class="card-body">

    <form method="POST" enctype="multipart/form-data">

      <div class="row">

        <div class="col-md-3">
          <label>Nama Pelanggan</label>
          <input type="text"
                 name="nama"
                 class="form-control"
                 placeholder="Nama Pelanggan"
                 required>
        </div>

        <div class="col-md-3">
          <label>Tanggal Servis</label>
          <input type="date"
                 name="tanggal"
                 class="form-control"
                 required>
        </div>

        <div class="col-md-3">
          <label>Merk Mobil</label>
          <input type="text"
                 name="merk"
                 class="form-control"
                 placeholder="Merk Mobil"
                 required>
        </div>

        <div class="col-md-3">
          <label>Tahun</label>
          <input type="text"
                 name="tahun"
                 class="form-control"
                 placeholder="Tahun"
                 required>
        </div>

      </div>

      <br>

      <div class="row">

        <div class="col-md-3">
          <label>Transmisi</label>
          <select name="transmisi"
                  class="form-control"
                  required>

            <option value="">Transmisi</option>
            <option value="Manual">Manual</option>
            <option value="Matic">Matic</option>

          </select>
        </div>

        <div class="col-md-3">
          <label>Plat Nomor</label>
          <input type="text"
                 name="plat"
                 class="form-control"
                 placeholder="Plat Nomor"
                 required>
        </div>

        <div class="col-md-6">
          <label>Keterangan Kerusakan</label>
          <input type="text"
                 name="keterangan"
                 class="form-control"
                 placeholder="Keterangan"
                 required>
        </div>

      </div>

      <br>

      <div class="row">

        <div class="col-md-3">
          <label>Harga Servis</label>
          <input type="text"
                name="harga"
                class="form-control rupiah"
                placeholder="Harga"
                required>
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

      <button name="simpan" class="btn btn-success">
        Simpan
      </button>

    </form>

  </div>
</div>

<br>

<!-- FILTER -->
<div class="card no-print">

  <div class="card-header bg-primary text-white">
    Filter & Laporan
  </div>

  <div class="card-body">

    <form method="GET">

      <div class="row">

        <div class="col-md-3">
          <input type="text"
                 name="nama"
                 class="form-control"
                 placeholder="Cari nama..."
                 value="<?= $_GET['nama'] ?? '' ?>">
        </div>

        <div class="col-md-3">
          <input type="text"
                 name="plat"
                 class="form-control"
                 placeholder="Cari plat..."
                 value="<?= $_GET['plat'] ?? '' ?>">
        </div>

        <div class="col-md-3">
          <input type="date"
                 name="tanggal"
                 class="form-control"
                 value="<?= $_GET['tanggal'] ?? '' ?>">
        </div>

        <div class="col-md-3">

          <button class="btn btn-primary">
            Cari
          </button>

          <a href="servis.php" class="btn btn-secondary">
            Reset
          </a>

          <button type="button"
                  onclick="window.print()"
                  class="btn btn-danger">
                  Cetak PDF
          </button>

        </div>

      </div>

    </form>

  </div>
</div>

<br>

<?php

// =========================
// SIMPAN
// =========================
if(isset($_POST['simpan'])){

  // UPLOAD NOTA
  $nota = '';

  if($_FILES['nota']['name'] != ''){

    $nama_file = time().'_'.$_FILES['nota']['name'];

    $tmp = $_FILES['nota']['tmp_name'];

    $folder = "../uploads/nota/";

    // buat folder otomatis
    if(!is_dir($folder)){
      mkdir($folder, 0777, true);
    }

    // upload file
    if(move_uploaded_file($tmp, $folder.$nama_file)){
      $nota = $nama_file;
    }

  }

  // SIMPAN DATABASE
  $simpan = mysqli_query($conn,"
    INSERT INTO servis
    (
      nama_pelanggan,
      tanggal_servis,
      merk,
      tahun,
      transmisi,
      plat_nomor,
      harga,
      keterangan,
      nota
    )
    VALUES(
      '$_POST[nama]',
      '$_POST[tanggal]',
      '$_POST[merk]',
      '$_POST[tahun]',
      '$_POST[transmisi]',
      '$_POST[plat]',
      '".str_replace('.','',$_POST['harga'])."',
      '$_POST[keterangan]',
      '$nota'
    )
  ");

  if($simpan){

    echo "
    <script>
      Swal.fire(
        'Berhasil!',
        'Data servis berhasil ditambahkan',
        'success'
      ).then(()=>{
        window.location='servis.php';
      });
    </script>
    ";

  } else {

    echo "
    <script>
      Swal.fire(
        'Gagal!',
        'Data gagal disimpan',
        'error'
      );
    </script>
    ";

  }

}

// =========================
// HAPUS
// =========================
if(isset($_GET['hapus'])){

  $ambil = mysqli_fetch_array(mysqli_query($conn,"
    SELECT * FROM servis
    WHERE id_servis='$_GET[hapus]'
  "));

  // hapus file nota
  if($ambil['nota'] != ''){

    $path = "../uploads/nota/".$ambil['nota'];

    if(file_exists($path)){
      unlink($path);
    }

  }

  mysqli_query($conn,"
    DELETE FROM servis
    WHERE id_servis='$_GET[hapus]'
  ");

  echo "
  <script>
    Swal.fire(
      'Terhapus!',
      'Data berhasil dihapus',
      'success'
    ).then(()=>{
      window.location='servis.php';
    });
  </script>
  ";

}

// =========================
// UPDATE
// =========================
if(isset($_POST['update'])){

  $nota_update = "";

  // upload nota baru jika ada
  if($_FILES['nota']['name'] != ''){

    $nama_file = time().'_'.$_FILES['nota']['name'];

    $tmp = $_FILES['nota']['tmp_name'];

    $folder = "../uploads/nota/";

    if(move_uploaded_file($tmp, $folder.$nama_file)){

      $nota_update = ", nota='$nama_file'";

    }

  }

  mysqli_query($conn,"
    UPDATE servis SET
      nama_pelanggan='$_POST[nama]',
      tanggal_servis='$_POST[tanggal]',
      merk='$_POST[merk]',
      tahun='$_POST[tahun]',
      transmisi='$_POST[transmisi]',
      plat_nomor='$_POST[plat]',
      harga='".str_replace('.','',$_POST['harga'])."',
      keterangan='$_POST[keterangan]'
      $nota_update
    WHERE id_servis='$_POST[id]'
  ");

  echo "
  <script>
    Swal.fire(
      'Berhasil!',
      'Data berhasil diupdate',
      'success'
    ).then(()=>{
      window.location='servis.php';
    });
  </script>
  ";

}

// =========================
// FILTER
// =========================
$where = "WHERE 1=1";

if(!empty($_GET['nama'])){
  $where .= " AND nama_pelanggan LIKE '%$_GET[nama]%'";
}

if(!empty($_GET['plat'])){
  $where .= " AND plat_nomor LIKE '%$_GET[plat]%'";
}

if(!empty($_GET['tanggal'])){
  $where .= " AND tanggal_servis='$_GET[tanggal]'";
}

?>

<!-- KOP -->
<div class="print-only text-center mb-3">

  <h4>LAPORAN DATA SERVIS</h4>

  <p>
    Tanggal: <?= date('d-m-Y') ?>
  </p>

  <hr>

</div>

<!-- TABEL -->
<div class="card">

  <div class="card-header no-print">
    Data Servis
  </div>

  <div class="card-body">

    <table class="table table-bordered">

      <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Tanggal</th>
        <th>Mobil</th>
        <th>Plat</th>
        <th>Keterangan</th>
        <th>Harga</th>
        <th class="no-print">Nota</th>
        <th class="no-print">Aksi</th>
      </tr>

<?php

$no = 1;

$data = mysqli_query($conn,"
  SELECT * FROM servis
  $where
");

while($d = mysqli_fetch_array($data)){
?>

<tr>

  <td><?= $no++ ?></td>

  <td><?= $d['nama_pelanggan'] ?></td>

  <td><?= $d['tanggal_servis'] ?></td>

  <td>
    <?= $d['merk'].' '.$d['tahun'].' ('.$d['transmisi'].')' ?>
  </td>

  <td><?= $d['plat_nomor'] ?></td>

  <td><?= $d['keterangan'] ?></td>

  <!-- HARGA -->
  <td>
    Rp <?= number_format($d['harga']) ?>
  </td>

  <!-- NOTA -->
  <td class="no-print">

      <?php if($d['nota'] != ''){ ?>

      <a href="../uploads/nota/<?= $d['nota'] ?>"
         target="_blank"
         class="btn btn-success btn-sm">
         Lihat Nota
      </a>

    <?php } else { ?>

      <span class="text-danger">
        Tidak ada
      </span>

    <?php } ?>

  </td>

  <!-- AKSI -->
  <td class="no-print">

    <a href="servis.php?edit=<?= $d['id_servis'] ?>"
       class="btn btn-warning btn-sm">
       Edit
    </a>

    <a href="javascript:void(0)"
       onclick="hapusData(<?= $d['id_servis'] ?>)"
       class="btn btn-danger btn-sm">
       Hapus
    </a>

  </td>

</tr>

<?php } ?>

    </table>

  </div>
</div>

<!-- FORM EDIT -->
<?php
if(isset($_GET['edit'])){

$e = mysqli_fetch_array(mysqli_query($conn,"
  SELECT * FROM servis
  WHERE id_servis='$_GET[edit]'
"));
?>

<div class="card mt-3 no-print">

  <div class="card-header bg-warning">
    Edit Servis
  </div>

  <div class="card-body">

    <form method="POST" enctype="multipart/form-data">

      <input type="hidden"
             name="id"
             value="<?= $e['id_servis'] ?>">

      <input type="text"
             name="nama"
             value="<?= $e['nama_pelanggan'] ?>"
             class="form-control mb-2">

      <input type="date"
             name="tanggal"
             value="<?= $e['tanggal_servis'] ?>"
             class="form-control mb-2">

      <input type="text"
             name="merk"
             value="<?= $e['merk'] ?>"
             class="form-control mb-2">

      <input type="text"
             name="tahun"
             value="<?= $e['tahun'] ?>"
             class="form-control mb-2">

      <select name="transmisi"
              class="form-control mb-2">

        <option value="Manual"
          <?= $e['transmisi']=='Manual'?'selected':'' ?>>
          Manual
        </option>

        <option value="Matic"
          <?= $e['transmisi']=='Matic'?'selected':'' ?>>
          Matic
        </option>

      </select>

      <input type="text"
             name="plat"
             value="<?= $e['plat_nomor'] ?>"
             class="form-control mb-2">

      <input type="text"
             name="keterangan"
             value="<?= $e['keterangan'] ?>"
             class="form-control mb-2">

      <input type="text"
            name="harga"
            value="<?= number_format($e['harga'],0,',','.') ?>"
            class="form-control rupiah mb-2">

      <label>Upload Nota Baru</label>

      <input type="file"
             name="nota"
             class="form-control mb-2"
             accept=".jpg,.jpeg,.png,.pdf">

      <button name="update"
              class="btn btn-primary">
              Perbaharui
      </button>

      <a href="servis.php"
         class="btn btn-secondary">
         Batal
      </a>

    </form>

  </div>
</div>

<?php } ?>

</div>

<script>
function hapusData(id){

  Swal.fire({
    title:'Yakin hapus?',
    text:'Data tidak bisa dikembalikan!',
    icon:'warning',
    showCancelButton:true,
    confirmButtonColor:'#d33',
    confirmButtonText:'Ya, hapus!',
    cancelButtonText:'Batal'
  }).then((result)=>{

    if(result.isConfirmed){

      window.location='servis.php?hapus='+id;

    }

  });

}
</script>

<?php include '../partials/footer.php'; ?>