<?php
include "../config/koneksi.php";

$tgl_awal=$_GET['tgl_awal']??'';
$tgl_akhir=$_GET['tgl_akhir']??'';

$where="WHERE 1=1";
if($tgl_awal!="" && $tgl_akhir!=""){
    $where.=" AND tanggal_servis BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

$q=mysqli_query($conn,"
SELECT nama_pelanggan, COUNT(*) AS frekuensi_service
FROM servis
$where
GROUP BY nama_pelanggan
ORDER BY frekuensi_service DESC,nama_pelanggan ASC
");

$totalPelanggan=0;
$totalService=0;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Laporan Pelanggan Service</title>
<style>
body{font-family:"Times New Roman",serif;font-size:12pt;margin:30px}
h2,h3{text-align:center;margin:2px}
hr{border:1px solid #000}
table{width:100%;border-collapse:collapse}
th,td{border:1px solid #000;padding:6px}
th{text-align:center}
.info td{border:none;padding:2px 5px}
.ttd td{border:none}
@media print{@page{margin:15mm}}
</style>
</head>
<body onload="window.print()">
<h2>PT. DEALER MOBIL</h2>
<h3>LAPORAN PELANGGAN SERVICE</h3>
<hr>
<table class="info">
<tr><td width="100">Periode</td><td>: <?=($tgl_awal!=""&&$tgl_akhir!="")?$tgl_awal." s/d ".$tgl_akhir:"Semua Periode";?></td></tr>
</table>
<br>
<table>
<thead>
<tr>
<th width="8%">No</th>
<th>Nama Pelanggan</th>
<th width="25%">Frekuensi Service</th>
</tr>
</thead>
<tbody>
<?php $no=1; while($d=mysqli_fetch_assoc($q)){ $totalPelanggan++; $totalService+=$d['frekuensi_service']; ?>
<tr>
<td align="center"><?=$no++;?></td>
<td><?=htmlspecialchars($d['nama_pelanggan']);?></td>
<td align="center"><?=$d['frekuensi_service'];?> Kali</td>
</tr>
<?php } ?>
</tbody>
</table>

<br><br>

<table class="info" style="width:45%;float:right">
<tr><td>Total Pelanggan</td><td>: <?=$totalPelanggan;?></td></tr>
<tr><td>Total Service</td><td>: <?=$totalService;?></td></tr>
</table>

<div style="clear:both"></div>

<br><br>

<table class="ttd" style="width:100%">
<tr>
<td width="60%"></td>
<td align="center">
Tasikmalaya, <?=date('d-m-Y');?><br><br>
Administrator
<br><br><br><br>
(................................)
</td>
</tr>
</table>
</body>
</html>