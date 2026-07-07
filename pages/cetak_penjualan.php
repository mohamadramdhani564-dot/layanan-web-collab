<?php
session_start();
if(!isset($_SESSION['login'])){
    header("Location: ../login.php");
}

include '../config/koneksi.php';

// FILTER
$where = "WHERE 1=1";

if(!empty($_GET['tanggal'])){
    $where .= " AND p.tanggal='".$_GET['tanggal']."'";
}

if(!empty($_GET['mobil'])){
    $where .= " AND (m.merk LIKE '%".$_GET['mobil']."%' OR m.tipe LIKE '%".$_GET['mobil']."%')";
}

if(!empty($_GET['tahun'])){
    $where .= " AND m.tahun='".$_GET['tahun']."'";
}

if(!empty($_GET['transmisi'])){
    $where .= " AND m.transmisi='".$_GET['transmisi']."'";
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Laporan Penjualan</title>

<style>

body{
    font-family:Arial, Helvetica, sans-serif;
    font-size:13px;
    margin:30px;
}

h2,h4,p{
    margin:0;
}

.judul{
    text-align:center;
    margin-bottom:20px;
}

hr{
    border:1px solid black;
    margin:10px 0 20px;
}

table{
    width:100%;
    border-collapse:collapse;
}

table th,
table td{
    border:1px solid #000;
    padding:8px;
    font-size:12px;
}

table th{
    background:#f2f2f2;
}

.right{
    text-align:right;
}

</style>

</head>

<body onload="window.print()">

<div class="judul">

    <h2>SHOWROOM MOBIL</h2>
    <h4>LAPORAN DATA PENJUALAN</h4>

    <p>Tanggal Cetak :
        <?= date('d-m-Y'); ?>
    </p>

</div>

<hr>

<table>

<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>Pelanggan</th>
    <th>Mobil</th>
    <th>Tahun</th>
    <th>Transmisi</th>
    <th>Harga</th>
    <th>Jumlah</th>
    <th>Total</th>
</tr>

<?php

$no = 1;
$grandtotal = 0;

$data = mysqli_query($conn,"
SELECT
p.*,
pl.nama,
m.*,
dp.jumlah
FROM penjualan p
JOIN pelanggan pl
ON p.id_pelanggan=pl.id_pelanggan
JOIN detail_penjualan dp
ON p.id_penjualan=dp.id_penjualan
JOIN mobil m
ON dp.id_mobil=m.id_mobil
$where
ORDER BY p.tanggal DESC
");

while($d=mysqli_fetch_array($data)){

$grandtotal += $d['total'];

?>

<tr>

<td align="center"><?= $no++; ?></td>

<td><?= date('d-m-Y',strtotime($d['tanggal'])); ?></td>

<td><?= $d['nama']; ?></td>

<td><?= $d['merk']." ".$d['tipe']; ?></td>

<td align="center"><?= $d['tahun']; ?></td>

<td align="center"><?= $d['transmisi']; ?></td>

<td class="right">
Rp <?= number_format($d['harga'],0,',','.'); ?>
</td>

<td align="center">
<?= $d['jumlah']; ?>
</td>

<td class="right">
Rp <?= number_format($d['total'],0,',','.'); ?>
</td>

</tr>

<?php } ?>

<tr>

<th colspan="8" class="right">
GRAND TOTAL
</th>

<th class="right">
Rp <?= number_format($grandtotal,0,',','.'); ?>
</th>

</tr>

</table>

<br><br><br>

<table style="width:100%; border:none;">
<tr style="border:none;">

<td style="border:none;"></td>

<td style="border:none; width:250px; text-align:center;">
Tasikmalaya, <?= date('d-m-Y'); ?>
<br><br><br><br><br>

<b>Administrator</b>

</td>

</tr>
</table>

</body>
</html>