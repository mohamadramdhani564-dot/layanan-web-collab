<?php
include "../config/koneksi.php";

$tgl_awal=$_GET['tgl_awal']??'';
$tgl_akhir=$_GET['tgl_akhir']??'';
$merk=$_GET['merk']??'';
$tipe=$_GET['tipe']??'';
$tahun=$_GET['tahun']??'';
$transmisi=$_GET['transmisi']??'';

$where="WHERE 1=1";

if($tgl_awal!="" && $tgl_akhir!=""){
    $where.=" AND p.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}
if($merk!=""){
    $where.=" AND m.merk='$merk'";
}
if($tipe!=""){
    $where.=" AND m.tipe='$tipe'";
}
if($tahun!=""){
    $where.=" AND m.tahun='$tahun'";
}
if($transmisi!=""){
    $where.=" AND m.transmisi='$transmisi'";
}

$query=mysqli_query($conn,"
SELECT
p.tanggal,
pl.nama,
m.merk,
m.tipe,
m.tahun,
m.transmisi,
m.harga,
p.total,
dp.jumlah
FROM penjualan p
JOIN pelanggan pl ON pl.id_pelanggan=p.id_pelanggan
JOIN detail_penjualan dp ON dp.id_penjualan=p.id_penjualan
JOIN mobil m ON m.id_mobil=dp.id_mobil
$where
ORDER BY p.tanggal ASC
");

$totalPendapatan=0;
$totalUnit=0;
$totalTransaksi=0;

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Cetak Laporan Penjualan</title>
<style>
body{font-family:"Times New Roman",serif;font-size:12pt;margin:30px}
h2,h3,p{text-align:center;margin:2px}
.info{margin:20px 0}
.info table{border:none}
.info td{padding:2px 8px}
table.laporan{width:100%;border-collapse:collapse}
table.laporan th,table.laporan td{border:1px solid #000;padding:6px}
table.laporan th{text-align:center}
.right{text-align:right}
.ttd{margin-top:40px;width:100%}
.ttd td{border:none}
@media print{
@page{margin:15mm}
}
</style>
</head>
<body onload="window.print()">

<h2>PT. DEALER MOBIL</h2>
<h3>LAPORAN PENJUALAN MOBIL</h3>
<hr>

<div class="info">
<table>
<tr><td>Periode</td><td>: <?= $tgl_awal!=""?$tgl_awal." s/d ".$tgl_akhir:"Semua Periode"; ?></td></tr>
<tr><td>Merk</td><td>: <?= $merk!=""?$merk:"Semua"; ?></td></tr>
<tr><td>Tipe</td><td>: <?= $tipe!=""?$tipe:"Semua"; ?></td></tr>
<tr><td>Tahun</td><td>: <?= $tahun!=""?$tahun:"Semua"; ?></td></tr>
<tr><td>Transmisi</td><td>: <?= $transmisi!=""?$transmisi:"Semua"; ?></td></tr>
</table>
</div>

<table class="laporan">
<thead>
<tr>
<th>No</th>
<th>Tanggal</th>
<th>Pelanggan</th>
<th>Merk</th>
<th>Tipe</th>
<th>Tahun</th>
<th>Transmisi</th>
<th>Harga</th>
<th>Total</th>
</tr>
</thead>
<tbody>
<?php $no=1; while($d=mysqli_fetch_assoc($query)):
$totalPendapatan+=$d['total'];
$totalUnit+=$d['jumlah'];
$totalTransaksi++;
?>
<tr>
<td><?= $no++; ?></td>
<td><?= date('d-m-Y',strtotime($d['tanggal'])); ?></td>
<td><?= htmlspecialchars($d['nama']); ?></td>
<td><?= htmlspecialchars($d['merk']); ?></td>
<td><?= htmlspecialchars($d['tipe']); ?></td>
<td><?= htmlspecialchars($d['tahun']); ?></td>
<td><?= htmlspecialchars($d['transmisi']); ?></td>
<td class="right">Rp <?= number_format($d['harga'],0,",","."); ?></td>
<td class="right">Rp <?= number_format($d['total'],0,",","."); ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<br>

<table style="width:40%;float:right">
<tr><td>Jumlah Transaksi</td><td>: <?= $totalTransaksi; ?></td></tr>
<tr><td>Jumlah Unit</td><td>: <?= $totalUnit; ?></td></tr>
<tr><td>Total Pendapatan</td><td>: Rp <?= number_format($totalPendapatan,0,",","."); ?></td></tr>
</table>

<div style="clear:both"></div>

<table class="ttd">
<tr>
<td style="width:60%"></td>
<td style="text-align:center">
Tasikmalaya, <?= date('d-m-Y'); ?><br><br>
Administrator
<br><br><br><br>
(................................)
</td>
</tr>
</table>

</body>
</html>
