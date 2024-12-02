<?php
// Proses tambah produk
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $kategori_id = $_POST['kategori_id'];

    $query = "INSERT INTO produk (nama_produk, harga, deskripsi, kategori_id) 
              VALUES ('$nama_produk', '$harga', '$deskripsi', '$kategori_id')";
    
    if ($conn->query($query) === TRUE) {
        echo "<script>alert('Produk berhasil ditambahkan!'); window.location = 'index.php';</script>";
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}
?>
