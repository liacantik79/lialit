<?php
require 'config.php';

if (isset($_GET['id'])) {
    $id_produk = $_GET['id'];

    // Ambil data produk
    $query = "SELECT * FROM produk WHERE id_produk = $id_produk";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $produk = $result->fetch_assoc();
    } else {
        echo "Produk tidak ditemukan.";
        exit();
    }

    // Ambil data kategori
    $kategoriQuery = "SELECT * FROM kategori";
    $kategoriResult = $conn->query($kategoriQuery);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_produk = $_POST['id_produk'];
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $kategori_id = $_POST['kategori_id'];

    $query = "UPDATE produk 
              SET nama_produk = '$nama_produk', harga = '$harga', deskripsi = '$deskripsi', kategori_id = '$kategori_id' 
              WHERE id_produk = $id_produk";

    if ($conn->query($query) === TRUE) {
        header('Location: index.php');
        exit();
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Edit Produk</h1>
        <form action="edit_produk.php" method="POST">
            <input type="hidden" name="id_produk" value="<?= $produk['id_produk'] ?>">
            <div class="mb-3">
                <label for="nama_produk" class="form-label">Nama Produk</label>
                <input type="text" class="form-control" id="nama_produk" name="nama_produk" value="<?= $produk['nama_produk'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="harga" class="form-label">Harga</label>
                <input type="number" class="form-control" id="harga" name="harga" value="<?= $produk['harga'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?= $produk['deskripsi'] ?></textarea>
            </div>
            <div class="mb-3">
                <label for="kategori_id" class="form-label">Kategori</label>
                <select class="form-select" id="kategori_id" name="kategori_id" required>
                    <?php while ($row = $kategoriResult->fetch_assoc()) { ?>
                        <option value="<?= $row['id_kategori'] ?>" <?= $row['id_kategori'] == $produk['kategori_id'] ? 'selected' : '' ?>>
                            <?= $row['nama_kategori'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Produk</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
