<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';
?>

<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="content-wrapper p-4">

    <h3 class="mb-4">Kelola Akun</h3>

<?php

// ====================================
// TAMBAH AKUN
// ====================================
if (isset($_POST['simpan'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);

    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if ($password != $confirm) {

        echo "
        <script>
        Swal.fire({
            icon:'error',
            title:'Gagal',
            text:'Konfirmasi password tidak cocok'
        });
        </script>
        ";

    } else {

        $cek = mysqli_query($conn,"
        SELECT * FROM admin
        WHERE username='$username'
        ");

        if(mysqli_num_rows($cek) > 0){

            echo "
            <script>
            Swal.fire({
                icon:'error',
                title:'Gagal',
                text:'Username sudah digunakan'
            });
            </script>
            ";

        } else {

            $password = md5($password);

            $simpan = mysqli_query($conn,"
            INSERT INTO admin(username,password)
            VALUES('$username','$password')
            ");

            if($simpan){

                echo "
                <script>
                Swal.fire({
                    icon:'success',
                    title:'Berhasil',
                    text:'Akun berhasil ditambahkan'
                }).then(()=>{
                    window.location='akun.php';
                });
                </script>
                ";

            }

        }

    }

}

// ====================================
// HAPUS AKUN
// ====================================
if (isset($_GET['hapus'])) {

    $username_hapus = $_GET['hapus'];

    $hapus = mysqli_query($conn, "
        DELETE FROM admin
        WHERE username='$username_hapus'
    ");

    if ($hapus) {

        echo "
        <script>
        Swal.fire({
            icon:'success',
            title:'Berhasil',
            text:'Akun berhasil dihapus'
        }).then(() => {
            window.location='akun.php';
        });
        </script>
        ";
    }
}

// ====================================
// UPDATE AKUN
// ====================================
if (isset($_POST['update'])) {

    $username_lama = $_POST['username_lama'];

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);

    $update = mysqli_query($conn, "
        UPDATE admin SET
        username='$username',
        password='$password'
        WHERE username='$username_lama'
    ");

    if ($update) {

        echo "
        <script>
        Swal.fire({
            icon:'success',
            title:'Berhasil',
            text:'Data akun berhasil diperbaharui'
        }).then(() => {
            window.location='akun.php';
        });
        </script>
        ";
    }
}

?>
<header><link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"></header>
    <!-- FORM TAMBAH -->
    <div class="card mb-4">

        <div class="card-header bg-primary text-white">
            Tambah Akun
        </div>

        <div class="card-body">

            <form method="POST">

                <div class="row">

                    <div class="col-md-4 mb-2">
                        <input type="text"
                            name="username"
                            class="form-control"
                            placeholder="Username"
                            required>
                    </div>

                    <div class="col-md-4 mb-2">

                        <div class="input-group">

                            <input type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                placeholder="Password"
                                required>

                            <div class="input-group-append">

                                <button type="button"
                                        class="btn btn-outline-secondary"
                                        onmousedown="lihatPassword('password')"
                                        onmouseup="sembunyiPassword('password')"
                                        onmouseleave="sembunyiPassword('password')">

                                    <i class="fa fa-eye"></i>

                                </button>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-4 mb-2">

                        <div class="input-group">

                            <input type="password"
                                id="confirm_password"
                                name="confirm_password"
                                class="form-control"
                                placeholder="Konfirmasi Password"
                                required>

                            <div class="input-group-append">

                                <button type="button"
                                        class="btn btn-outline-secondary"
                                        onmousedown="lihatPassword('confirm_password')"
                                        onmouseup="sembunyiPassword('confirm_password')"
                                        onmouseleave="sembunyiPassword('confirm_password')">

                                    <i class="fa fa-eye"></i>

                                </button>

                            </div>

                        </div>

                    </div>

                </div>

                <button type="submit"
                        name="simpan"
                        class="btn btn-success">
                    Simpan
                </button>

            </form>

        </div>
    </div>

    <!-- TABEL -->
    <div class="card">

        <div class="card-header">
            Daftar Akun
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-striped">

                    <thead>

                        <tr>
                            <th width="5%">No</th>
                            <th>Username</th>
                            <th width="20%">Aksi</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php
                    $no = 1;

                    $data = mysqli_query($conn, "
                        SELECT * FROM admin
                    ");

                    while ($d = mysqli_fetch_array($data)) {
                    ?>

                        <tr>

                            <td><?= $no++; ?></td>

                            <td><?= $d['username']; ?></td>

                            <td>

                                <a href="akun.php?edit=<?= $d['username']; ?>"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <button
                                    onclick="hapusData('<?= $d['username']; ?>')"
                                    class="btn btn-danger btn-sm">
                                    Hapus
                                </button>

                            </td>

                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>
    </div>

<?php
if (isset($_GET['edit'])) {

    $username_edit = $_GET['edit'];

    $edit = mysqli_query($conn, "
        SELECT * FROM admin
        WHERE username='$username_edit'
    ");

    $e = mysqli_fetch_array($edit);
?>

    <!-- FORM EDIT -->
    <div class="card mt-4">

        <div class="card-header bg-warning">
            Edit Akun
        </div>

        <div class="card-body">

            <form method="POST">

                <input type="hidden"
                       name="username_lama"
                       value="<?= $e['username']; ?>">

                <div class="row">

                    <div class="col-md-6 mb-2">

                        <input type="text"
                            name="username"
                            class="form-control"
                            value="<?= $e['username']; ?>"
                            required>

                    </div>

                    <div class="col-md-6 mb-2">

                        <div class="input-group">

                            <input type="password"
                                id="edit_password"
                                name="password"
                                class="form-control"
                                placeholder="Password Baru"
                                required>

                            <div class="input-group-append">

                                <button type="button"
                                        class="btn btn-outline-secondary"
                                        onmousedown="lihatPassword('edit_password')"
                                        onmouseup="sembunyiPassword('edit_password')"
                                        onmouseleave="sembunyiPassword('edit_password')">

                                    <i class="fa fa-eye"></i>

                                </button>

                            </div>

                        </div>

                    </div>

                </div>

                <button type="submit"
                        name="update"
                        class="btn btn-primary">
                    Update
                </button>

                <a href="akun.php"
                   class="btn btn-secondary">
                    Batal
                </a>

            </form>

        </div>
    </div>

<?php } ?>

</div>

<script>

function hapusData(username){

    Swal.fire({
        title: 'Yakin hapus?',
        text: 'Data akun akan dihapus!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {

        if(result.isConfirmed){

            window.location =
            'akun.php?hapus=' + username;

        }

    });

}

</script>
<script>

function lihatPassword(id){
    document.getElementById(id).type = 'text';
}

function sembunyiPassword(id){
    document.getElementById(id).type = 'password';
}

</script>
<?php include '../partials/footer.php'; ?>