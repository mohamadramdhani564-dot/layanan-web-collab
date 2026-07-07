<?php
session_start();
if(!isset($_SESSION['login'])){
    header("Location: ../login.php");
    exit;
}
include 'config/koneksi.php';
include 'partials/header.php';
include 'partials/sidebar.php';

/* CARD */
$jml_mobil=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM mobil"))['total'];
$jml_pelanggan=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM pelanggan"))['total'];
$jml_penjualan=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM penjualan"))['total'];
$jml_servis=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM servis"))['total'];

/* CHART PENJUALAN */
$qPen=mysqli_query($conn,"SELECT MONTH(tanggal) bln,SUM(total) total FROM penjualan GROUP BY MONTH(tanggal) ORDER BY MONTH(tanggal)");
$lblPen=[];$valPen=[];
while($r=mysqli_fetch_assoc($qPen)){
    $lblPen[]=date('M',mktime(0,0,0,$r['bln'],1));
    $valPen[]=$r['total'];
}

/* CHART SERVICE */
$qSrv=mysqli_query($conn,"SELECT MONTH(tanggal_servis) bln,COUNT(*) jml FROM servis GROUP BY MONTH(tanggal_servis) ORDER BY MONTH(tanggal_servis)");
$lblSrv=[];$valSrv=[];
while($r=mysqli_fetch_assoc($qSrv)){
    $lblSrv[]=date('M',mktime(0,0,0,$r['bln'],1));
    $valSrv[]=$r['jml'];
}

$penjualan=mysqli_query($conn,"
SELECT p.tanggal,pl.nama,m.merk,m.tipe,p.total
FROM penjualan p
JOIN pelanggan pl ON pl.id_pelanggan=p.id_pelanggan
JOIN detail_penjualan dp ON dp.id_penjualan=p.id_penjualan
JOIN mobil m ON m.id_mobil=dp.id_mobil
ORDER BY p.id_penjualan DESC LIMIT 5");

$servis=mysqli_query($conn,"
SELECT tanggal_servis,nama_pelanggan,merk,harga
FROM servis
ORDER BY id_servis DESC LIMIT 5");
?>
<div class="content-wrapper p-3">
<section class="content-header">
<div class="container-fluid">
<h2>Dashboard</h2>
<p class="text-muted">Selamat Datang di Sistem Informasi Dealer Mobil</p>
</div>
</section>

<section class="content">
<div class="container-fluid">

<div class="row">
<?php
$cards=[
['bg-info','fas fa-car',$jml_mobil,'Total Mobil','pages/mobil.php'],
['bg-success','fas fa-users',$jml_pelanggan,'Total Pelanggan','pages/pelanggan.php'],
['bg-warning','fas fa-cash-register',$jml_penjualan,'Total Penjualan','pages/penjualan.php'],
['bg-danger','fas fa-tools',$jml_servis,'Total Service','pages/servis.php']
];
foreach($cards as $c){ ?>
<div class="col-lg-3 col-6">
<div class="small-box <?=$c[0]?>">
<div class="inner">
<h3><?=$c[2]?></h3>
<p><?=$c[3]?></p>
</div>
<div class="icon"><i class="<?=$c[1]?>"></i></div>
<a href="<?=$c[4]?>" class="small-box-footer">Lihat Data <i class="fas fa-arrow-circle-right"></i></a>
</div>
</div>
<?php } ?>
</div>

<div class="row">
<div class="col-md-6">
<div class="card card-primary">
<div class="card-header"><h3 class="card-title">Grafik Penjualan</h3></div>
<div class="card-body"><canvas id="chartPenjualan" height="170"></canvas></div>
</div>
</div>

<div class="col-md-6">
<div class="card card-success">
<div class="card-header"><h3 class="card-title">Grafik Service</h3></div>
<div class="card-body"><canvas id="chartService" height="170"></canvas></div>
</div>
</div>
</div>

<div class="row">
<div class="col-md-6">
<div class="card">
<div class="card-header"><h3 class="card-title">5 Penjualan Terbaru</h3></div>
<div class="card-body table-responsive">
<table class="table table-bordered table-sm">
<thead><tr><th>Tanggal</th><th>Pelanggan</th><th>Mobil</th><th>Total</th></tr></thead>
<tbody>
<?php while($d=mysqli_fetch_assoc($penjualan)){ ?>
<tr>
<td><?=date('d-m-Y',strtotime($d['tanggal']))?></td>
<td><?=$d['nama']?></td>
<td><?=$d['merk'].' '.$d['tipe']?></td>
<td>Rp <?=number_format($d['total'],0,",",".")?></td>
</tr>
<?php } ?>
</tbody>
</table>
</div></div></div>

<div class="col-md-6">
<div class="card">
<div class="card-header"><h3 class="card-title">5 Service Terbaru</h3></div>
<div class="card-body table-responsive">
<table class="table table-bordered table-sm">
<thead><tr><th>Tanggal</th><th>Pelanggan</th><th>Mobil</th><th>Biaya</th></tr></thead>
<tbody>
<?php while($d=mysqli_fetch_assoc($servis)){ ?>
<tr>
<td><?=date('d-m-Y',strtotime($d['tanggal_servis']))?></td>
<td><?=$d['nama_pelanggan']?></td>
<td><?=$d['merk']?></td>
<td>Rp <?=number_format($d['harga'],0,",",".")?></td>
</tr>
<?php } ?>
</tbody>
</table>
</div></div></div>
</div>

</div>
</section>
</div>

<?php include 'partials/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('chartPenjualan'),{
type:'bar',
data:{labels:<?=json_encode($lblPen)?>,
datasets:[{label:'Pendapatan',data:<?=json_encode($valPen)?>}]},
options:{responsive:true}
});
new Chart(document.getElementById('chartService'),{
type:'line',
data:{labels:<?=json_encode($lblSrv)?>,
datasets:[{label:'Jumlah Service',data:<?=json_encode($valSrv)?>,fill:false}]},
options:{responsive:true}
});
</script>
