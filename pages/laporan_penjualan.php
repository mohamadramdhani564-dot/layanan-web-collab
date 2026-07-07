<?php
include "../config/koneksi.php";
include "../partials/header.php";
include "../partials/sidebar.php";

/* ===========================
   FILTER
=========================== */

$tgl_awal  = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : "";
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : "";

$merk       = isset($_GET['merk']) ? $_GET['merk'] : "";
$tipe       = isset($_GET['tipe']) ? $_GET['tipe'] : "";
$tahun      = isset($_GET['tahun']) ? $_GET['tahun'] : "";
$transmisi  = isset($_GET['transmisi']) ? $_GET['transmisi'] : "";

$where = "WHERE 1=1";

if($tgl_awal != "" && $tgl_akhir != "")
{
    $where .= " AND p.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

if($merk != "")
{
    $where .= " AND m.merk='$merk'";
}

if($tipe != "")
{
    $where .= " AND m.tipe='$tipe'";
}

if($tahun != "")
{
    $where .= " AND m.tahun='$tahun'";
}

if($transmisi != "")
{
    $where .= " AND m.transmisi='$transmisi'";
}

/* ===========================
   STATISTIK
=========================== */

$statistik = mysqli_query($conn,"

SELECT

COUNT(DISTINCT p.id_penjualan) AS transaksi,

SUM(dp.jumlah) AS unit,

SUM(p.total) AS pendapatan,

AVG(p.total) AS rata

FROM penjualan p

JOIN detail_penjualan dp
ON dp.id_penjualan=p.id_penjualan

JOIN mobil m
ON m.id_mobil=dp.id_mobil

$where

");

$s = mysqli_fetch_assoc($statistik);

$total_transaksi = $s['transaksi'];

$total_unit = $s['unit'];

$total_pendapatan = $s['pendapatan'];

$rata_transaksi = $s['rata'];

if($total_transaksi=="") $total_transaksi=0;
if($total_unit=="") $total_unit=0;
if($total_pendapatan=="") $total_pendapatan=0;
if($rata_transaksi=="") $rata_transaksi=0;


/* ===========================
   DATA PENJUALAN
=========================== */
$qMerk = mysqli_query($conn,"SELECT DISTINCT merk FROM mobil ORDER BY merk");
$qTipe = mysqli_query($conn,"SELECT DISTINCT tipe FROM mobil ORDER BY tipe");
$qTahun = mysqli_query($conn,"SELECT DISTINCT tahun FROM mobil ORDER BY tahun DESC");
$qTransmisi = mysqli_query($conn,"SELECT DISTINCT transmisi FROM mobil ORDER BY transmisi");
$data = mysqli_query($conn,"

SELECT

p.id_penjualan,

p.tanggal,

p.total,

pl.nama,

m.merk,

m.tipe,

m.tahun,

m.transmisi,

m.harga,

dp.jumlah

FROM penjualan p

JOIN pelanggan pl
ON pl.id_pelanggan=p.id_pelanggan

JOIN detail_penjualan dp
ON dp.id_penjualan=p.id_penjualan

JOIN mobil m
ON m.id_mobil=dp.id_mobil

$where

ORDER BY p.id_penjualan DESC

");



/* ===========================
   TOTAL FOOTER
=========================== */

$grand_total = 0;

$total_qty = 0;

?>
<div class="content-wrapper">

    <section class="content-header">
        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-chart-bar"></i>
                        Laporan Penjualan Mobil
                    </h1>
                </div>

            </div>

        </div>
    </section>

    <section class="content">

        <div class="container-fluid">

            <!-- CARD -->

            <div class="row">

                <div class="col-lg-3 col-6">

                    <div class="small-box bg-info">

                        <div class="inner">

                            <h3><?= number_format($total_transaksi); ?></h3>

                            <p>Total Transaksi</p>

                        </div>

                        <div class="icon">

                            <i class="fas fa-shopping-cart"></i>

                        </div>

                    </div>

                </div>

                <div class="col-lg-3 col-6">

                    <div class="small-box bg-success">

                        <div class="inner">

                            <h3><?= number_format($total_unit); ?></h3>

                            <p>Unit Terjual</p>

                        </div>

                        <div class="icon">

                            <i class="fas fa-car"></i>

                        </div>

                    </div>

                </div>

                <div class="col-lg-3 col-6">

                    <div class="small-box bg-warning">

                        <div class="inner">

                            <h3>

                                Rp <?= number_format($total_pendapatan,0,",","."); ?>

                            </h3>

                            <p>Total Pendapatan</p>

                        </div>

                        <div class="icon">

                            <i class="fas fa-money-bill-wave"></i>

                        </div>

                    </div>

                </div>

                <div class="col-lg-3 col-6">

                    <div class="small-box bg-danger">

                        <div class="inner">

                            <h3>

                                Rp <?= number_format($rata_transaksi,0,",","."); ?>

                            </h3>

                            <p>Rata-rata Transaksi</p>

                        </div>

                        <div class="icon">

                            <i class="fas fa-chart-line"></i>

                        </div>

                    </div>

                </div>

            </div>

            <!-- FILTER -->

            <div class="card">

                <div class="card-header bg-primary">

                    <h3 class="card-title">

                        Filter Laporan

                    </h3>

                </div>

                <div class="card-body">

                    <form method="GET">

                        <div class="row">

                            <div class="col-md-2">
                                <label>Tanggal Awal</label>
                                <input type="date" name="tgl_awal" class="form-control" value="<?= $tgl_awal; ?>">
                            </div>

                            <div class="col-md-2">
                                <label>Tanggal Akhir</label>
                                <input type="date" name="tgl_akhir" class="form-control" value="<?= $tgl_akhir; ?>">
                            </div>

                            <div class="col-md-2">
                                <label>Merk</label>
                                <select name="merk" class="form-control">
                                    <option value="">-- Semua Merk --</option>
                                    <?php while($m=mysqli_fetch_assoc($qMerk)){ ?>
                                        <option value="<?= $m['merk']; ?>" <?= ($merk==$m['merk'])?'selected':''; ?>>
                                            <?= $m['merk']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>Tipe</label>
                                <select name="tipe" class="form-control">
                                    <option value="">-- Semua Tipe --</option>
                                    <?php while($t=mysqli_fetch_assoc($qTipe)){ ?>
                                        <option value="<?= $t['tipe']; ?>" <?= ($tipe==$t['tipe'])?'selected':''; ?>>
                                            <?= $t['tipe']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>Tahun</label>
                                <select name="tahun" class="form-control">
                                    <option value="">-- Semua Tahun --</option>
                                    <?php while($th=mysqli_fetch_assoc($qTahun)){ ?>
                                        <option value="<?= $th['tahun']; ?>" <?= ($tahun==$th['tahun'])?'selected':''; ?>>
                                            <?= $th['tahun']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>Transmisi</label>
                                <select name="transmisi" class="form-control">
                                    <option value="">-- Semua --</option>
                                    <?php while($tr=mysqli_fetch_assoc($qTransmisi)){ ?>
                                        <option value="<?= $tr['transmisi']; ?>" <?= ($transmisi==$tr['transmisi'])?'selected':''; ?>>
                                            <?= $tr['transmisi']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                        </div>

                        <br>

                        <div class="row">

                            <div class="col-md-12">

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>

                                <a href="laporan_penjualan.php" class="btn btn-secondary">
                                    <i class="fas fa-sync"></i> Reset
                                </a>

                                <a href="print_penjualan.php?tgl_awal=<?= $tgl_awal; ?>&tgl_akhir=<?= $tgl_akhir; ?>&merk=<?= urlencode($merk); ?>&tipe=<?= urlencode($tipe); ?>&tahun=<?= urlencode($tahun); ?>&transmisi=<?= urlencode($transmisi); ?>" target="_blank" class="btn btn-success">
                                    <i class="fas fa-print"></i> Cetak
                                </a>

                            </div>

                        </div>

                    </form>

                </div>

            </div>


            <!-- TABEL -->

            <div class="card">

                <div class="card-header bg-primary">

                    <h3 class="card-title">

                        Data Penjualan

                    </h3>

                </div>

                <div class="card-body table-responsive">

                    <table
                        id="example1"
                        class="table table-bordered table-hover">

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

                                <th>Qty</th>

                                <th>Total</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php

                            $no=1;

                            while($d=mysqli_fetch_assoc($data))
                            {

                                $grand_total += $d['total'];

                                $total_qty += $d['jumlah'];

                            ?>

                            <tr>

                                <td><?= $no++; ?></td>

                                <td><?= date('d-m-Y',strtotime($d['tanggal'])); ?></td>

                                <td><?= $d['nama']; ?></td>

                                <td><?= $d['merk']; ?></td>

                                <td><?= $d['tipe']; ?></td>

                                <td><?= $d['tahun']; ?></td>

                                <td><?= $d['transmisi']; ?></td>

                                <td>

                                    Rp <?= number_format($d['harga'],0,",","."); ?>

                                </td>

                                <td><?= $d['jumlah']; ?></td>

                                <td>

                                    Rp <?= number_format($d['total'],0,",","."); ?>

                                </td>

                            </tr>

                            <?php } ?>

                        </tbody>

                        <tfoot>

                            <tr class="bg-light">

                                <th colspan="8" class="text-right">

                                    TOTAL

                                </th>

                                <th>

                                    <?= $total_qty; ?>

                                </th>

                                <th>

                                    Rp <?= number_format($grand_total,0,",","."); ?>

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
<!-- Chart JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(function () {

    // ==========================
    // DATATABLE
    // ==========================
    $("#example1").DataTable({
        responsive: true,
        autoWidth: false,
        lengthChange: true,
        pageLength: 10,
        ordering: true,
        searching: true,
        language: {
            search: "Cari : ",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Data tidak ditemukan",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            paginate: {
                first: "Awal",
                last: "Akhir",
                next: "»",
                previous: "«"
            }
        }
    });


    // ==========================
    // CHART PENJUALAN
    // ==========================

    const ctx = document.getElementById('grafikPenjualan');

    new Chart(ctx, {

        type: 'bar',

        data: {

            labels: <?= json_encode($bulan); ?>,

            datasets: [{

                label: 'Pendapatan Penjualan',

                data: <?= json_encode($total); ?>,

                backgroundColor: [
                    '#0d6efd',
                    '#198754',
                    '#ffc107',
                    '#dc3545',
                    '#20c997',
                    '#6f42c1',
                    '#fd7e14',
                    '#0dcaf0',
                    '#6610f2',
                    '#198754',
                    '#dc3545',
                    '#0d6efd'
                ],

                borderWidth: 1

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {

                    display: true,

                    position: 'top'

                }

            },

            scales: {

                y: {

                    beginAtZero: true

                }

            }

        }

    });

});



// ==========================
// PRINT
// ==========================

function cetakLaporan()
{
    window.print();
}



// ==========================
// VALIDASI FILTER
// ==========================

$('form').submit(function(){

    var awal = $('input[name=tgl_awal]').val();

    var akhir = $('input[name=tgl_akhir]').val();

    if(awal != "" && akhir == "")
    {
        alert("Tanggal akhir belum dipilih.");
        return false;
    }

    if(awal == "" && akhir != "")
    {
        alert("Tanggal awal belum dipilih.");
        return false;
    }

    return true;

});



// ==========================
// HOVER EFFECT TABLE
// ==========================

$("#example1 tbody tr").hover(

function(){

    $(this).css("background","#f8f9fa");

},

function(){

    $(this).css("background","");

});



// ==========================
// FORMAT PRINT
// ==========================

var style = document.createElement('style');

style.innerHTML = `
@media print{

    .main-sidebar,
    .main-header,
    .content-header,
    .btn,
    .dataTables_filter,
    .dataTables_length,
    .dataTables_paginate,
    .dataTables_info,
    .card-header{

        display:none !important;

    }

    .content-wrapper{

        margin-left:0 !important;

    }

    table{

        width:100%;

        border-collapse:collapse;

    }

    table th,
    table td{

        border:1px solid #000;

        padding:6px;

    }

    h1{

        text-align:center;

    }

}
`;

document.head.appendChild(style);

</script>