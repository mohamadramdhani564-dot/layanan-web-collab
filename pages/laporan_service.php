<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';
include '../partials/header.php';
include '../partials/sidebar.php';

$tgl_awal = $_GET['tgl_awal'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';

$where = "";

if ($tgl_awal != "" && $tgl_akhir != "") {
    $where = "WHERE tanggal_servis BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

$stat = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT
COUNT(*) total_service,
COALESCE(SUM(harga),0) total_pendapatan,
COALESCE(AVG(harga),0) rata_biaya,
SUM(CASE WHEN tanggal_servis=CURDATE() THEN 1 ELSE 0 END) service_hari_ini
FROM servis
$where
"));

$data = mysqli_query($conn,"
SELECT *
FROM servis
$where
ORDER BY tanggal_servis DESC,id_servis DESC
");
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="content-wrapper">

<section class="content-header">
<div class="container-fluid">
<h1><i class="fas fa-tools"></i> Laporan Service</h1>
</div>
</section>

<section class="content">
<div class="container-fluid">

<!-- CARD STATISTIK -->
<div class="row no-print">

<div class="col-lg-3 col-6">
<div class="small-box bg-info">
<div class="inner">
<h3><?=number_format($stat['total_service'])?></h3>
<p>Total Service</p>
</div>
<div class="icon">
<i class="fas fa-tools"></i>
</div>
</div>
</div>

<div class="col-lg-3 col-6">
<div class="small-box bg-success">
<div class="inner">
<h3>Rp <?=number_format($stat['total_pendapatan'],0,",",".")?></h3>
<p>Total Pendapatan</p>
</div>
<div class="icon">
<i class="fas fa-money-bill-wave"></i>
</div>
</div>
</div>

<div class="col-lg-3 col-6">
<div class="small-box bg-warning">
<div class="inner">
<h3><?=number_format($stat['service_hari_ini'])?></h3>
<p>Service Hari Ini</p>
</div>
<div class="icon">
<i class="fas fa-calendar-day"></i>
</div>
</div>
</div>

<div class="col-lg-3 col-6">
<div class="small-box bg-danger">
<div class="inner">
<h3>Rp <?=number_format($stat['rata_biaya'],0,",",".")?></h3>
<p>Rata-rata Biaya</p>
</div>
<div class="icon">
<i class="fas fa-chart-line"></i>
</div>
</div>
</div>

</div>

<!-- FILTER -->
<div class="card no-print">

<div class="card-header">
<h3 class="card-title">Filter</h3>
</div>

<div class="card-body">

<form method="get">

<div class="row">

<div class="col-md-4">
<label>Tanggal Awal</label>
<input type="date" name="tgl_awal" class="form-control" value="<?=$tgl_awal?>">
</div>

<div class="col-md-4">
<label>Tanggal Akhir</label>
<input type="date" name="tgl_akhir" class="form-control" value="<?=$tgl_akhir?>">
</div>

<div class="col-md-4 pt-4">

<button class="btn btn-primary">
<i class="fas fa-search"></i> Filter
</button>

<a href="laporan_service.php" class="btn btn-secondary">
Reset
</a>

<button type="button" onclick="window.print()" class="btn btn-success">
<i class="fas fa-print"></i> Cetak
</button>

<a href="print_customer_service.php?tgl_awal=<?=urlencode($tgl_awal)?>&tgl_akhir=<?=urlencode($tgl_akhir)?>" target="_blank" class="btn btn-info">
<i class="fas fa-users"></i> Cetak Customer
</a>

</div>

</div>

</form>

</div>
</div>

<!-- CETAK -->
<div class="print-title" style="display:none;">
<h2>LAPORAN SERVICE</h2>

<?php
if($tgl_awal!="" && $tgl_akhir!=""){
echo "<p>Periode : ".date('d-m-Y',strtotime($tgl_awal))." s/d ".date('d-m-Y',strtotime($tgl_akhir))."</p>";
}else{
echo "<p>Semua Data</p>";
}
?>

<hr>

</div>

<!-- TABEL -->

<div class="card">

<div class="card-header">
<h3 class="card-title">Data Service</h3>
</div>

<div class="card-body table-responsive">

<table class="table table-bordered table-hover" id="example1">

<thead>

<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>Pelanggan</th>
    <th>Merk</th>
    <th>Tahun</th>
    <th>Transmisi</th>
    <th>Plat</th>
    <th>Keterangan</th>
    <th>Biaya</th>
</tr>

</thead>

<tbody>

<?php

$no=1;
$grand=0;

while($d=mysqli_fetch_assoc($data)){

$grand+=$d['harga'];

?>

<tr>

<td><?=$no++?></td>

<td><?=date('d-m-Y',strtotime($d['tanggal_servis']))?></td>

<td><?=$d['nama_pelanggan']?></td>

<td><?=$d['merk']?></td>

<td><?=$d['tahun']?></td>

<td><?=$d['transmisi']?></td>

<td><?=$d['plat_nomor']?></td>

<td><?=$d['keterangan']?></td>

<td>Rp <?=number_format($d['harga'],0,",",".")?></td>

</tr>

<?php } ?>

</tbody>

<tfoot>

<tr>

<th colspan="8" class="text-right">
TOTAL PENDAPATAN
</th>

<th>
Rp <?=number_format($grand,0,",",".")?>
</th>

</tr>

</tfoot>

</table>

</div>

</div>

</div>

</section>

</div>

<?php include '../partials/footer.php'; ?>

<script>
$(function(){

if($.fn.DataTable){

$('#example1').DataTable({

responsive:true,
autoWidth:false,

language:{
search:"Cari:",
lengthMenu:"Tampilkan _MENU_ data",
zeroRecords:"Data tidak ditemukan"
}

});

}

});
</script>

<style>

@media print{

body{
background:#fff;
}

.print-title{
display:block !important;
text-align:center;
margin-bottom:20px;
}

.no-print,
.main-sidebar,
.main-header,
.main-footer,
.content-header{
display:none !important;
}

.content-wrapper{
margin-left:0 !important;
padding:0 !important;
}

.card{
border:none !important;
box-shadow:none !important;
}

.card-header{
display:none;
}

.table{
width:100%;
border-collapse:collapse;
}

.table th,
.table td{
border:1px solid #000 !important;
padding:8px !important;
font-size:12px;
color:#000 !important;
}

}
</style>