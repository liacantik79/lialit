<?php
$host = "localhost"; // Ganti dengan host database Anda
$user = "root";      // Username database Anda
$pass = "";          // Password database Anda
$db   = "toko_baju"; // Nama database

// Membuat koneksi
$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
