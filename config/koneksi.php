<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "dealer_mobil";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

?>