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
body{
    font-family:"Times New Roman", serif;
    font-size:12;
    margin:30px;
}

.judul{
    text-align:center;
    margin-bottom:25px;
}

.judul h2{
    margin:0;
    font-size:24pt;
    font-weight:bold;
}

.periode{
    margin-top:10px;
    font-size:13pt;
    font-weight:bold;
}

.deskripsi{
    width:85%;
    margin:15px auto;
    font-size:12pt;
    line-height:1.6;
    text-align:center;
}

hr{
    margin-top:18px;
    margin-bottom:25px;
}

table{
    width:100%;
    border-collapse:collapse;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
}

table th,
table td{
    border:1px solid #000;
    padding:8px 10px;
    vertical-align:middle;
    font-size:11pt;
}

table th{
    text-align:center;
    font-weight:bold;
    background:#f5f5f5;
}

.right{
    text-align:right;
}

.center{
    text-align:center;
}

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

<div class="judul">

    <h2>LAPORAN PENJUALAN MOBIL</h2>

    <div class="periode">
        Periode :
        <?php
        if($tgl_awal != "" && $tgl_akhir != ""){
            echo date('d-m-Y',strtotime($tgl_awal));
            echo " s/d ";
            echo date('d-m-Y',strtotime($tgl_akhir));
        }else{
            echo "Semua Periode";
        }
        ?>
    </div>

    <div class="deskripsi">
        Laporan ini menyajikan rekapitulasi data penjualan mobil berdasarkan periode dan filter yang dipilih.
    </div>

    <hr>

</div>
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
<td style="white-space:nowrap;">
    <?= htmlspecialchars($d['nama']); ?>
</td>
<td><?= htmlspecialchars($d['merk']); ?></td>
<td><?= htmlspecialchars($d['tipe']); ?></td>
<td><?= htmlspecialchars($d['tahun']); ?></td>
<td><?= htmlspecialchars($d['transmisi']); ?></td>
<td class="right" style="white-space:nowrap;">
    Rp <?= number_format($d['harga'],0,",","."); ?>
</td>
<td class="right" style="white-space:nowrap;">
    Rp <?= number_format($d['total'],0,",","."); ?>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<br>

<table style="
width:320px;
float:right;
margin-top:20px;
border-collapse:collapse;
font-size:12pt;
">
<tr>
    <td><strong>Jumlah Transaksi</strong></td>
    <td class="right"><?= $totalTransaksi; ?></td>
</tr>

<tr>
    <td><strong>Jumlah Unit</strong></td>
    <td class="right"><?= $totalUnit; ?></td>
</tr>

<tr>
    <td><strong>Total Pendapatan</strong></td>
    <td class="right">
        Rp <?= number_format($totalPendapatan,0,",","."); ?>
    </td>
</tr>
</table>

<div style="clear:both"></div>

<table class="ttd">
<tr>
<td style="width:60%"></td>
<td style="text-align:center">
Tasikmalaya, <?= date('d-m-Y'); ?><br><br>
Administrator

<br><br><br><br><br>

(____________________)
</td>
</tr>
</table>

</body>
</html>
