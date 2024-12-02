<?php
session_start();
require 'config.php'; // Include config.php untuk koneksi database

// Jika ada produk yang ditambahkan ke keranjang
if (isset($_POST['tambah_ke_keranjang'])) {
    $produk_id = $_POST['produk_id']; // ID produk yang ditambahkan
    $kuantitas = $_POST['kuantitas']; // Kuantitas yang dipilih

    // Jika keranjang belum ada, buat array kosong untuk keranjang
    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }

    // Cek apakah produk sudah ada di keranjang
    $produk_ditemukan = false;
    foreach ($_SESSION['keranjang'] as &$item) {
        if ($item['produk_id'] == $produk_id) {
            // Jika produk sudah ada, update kuantitasnya
            $item['kuantitas'] += $kuantitas;
            $produk_ditemukan = true;
            break;
        }
    }

    // Jika produk belum ada, tambahkan produk baru
    if (!$produk_ditemukan) {
        $_SESSION['keranjang'][] = [
            'produk_id' => $produk_id,
            'kuantitas' => $kuantitas
        ];
    }

    // Redirect untuk menampilkan modal keranjang
    header("Location: index.php#cartModal");
    exit();
}

// Ambil data produk untuk ditampilkan di halaman
$query = "SELECT id_produk, nama_produk, harga FROM produk";
$produkResult = $conn->query($query);

require 'config.php';

// Ambil data produk
$query = "SELECT produk.id_produk, produk.nama_produk, produk.harga, produk.deskripsi, kategori.nama_kategori
          FROM produk
          JOIN kategori ON produk.kategori_id = kategori.id_kategori";
$produkResult = $conn->query($query);

// Ambil data kategori untuk form tambah produk
$kategoriQuery = "SELECT * FROM kategori";
$kategoriResult = $conn->query($kategoriQuery);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>ThinkHappy Store</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    <body>
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container px-4 px-lg-5">
                <a class="navbar-brand" href="index.php">THINK HAPPY STORE</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                        <li class="nav-item">   
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Header-->
        <header class="bg-dark py-1">
            <div class="container px-2 px-lg-3 my-3">
                <div class="text-center text-white">
                    <h1 class="display-4 fw-bolder">Welcome to our shop</h1>
                    <p class="lead fw-normal text-white-50 mb-0">Let You Enjoy to shopping!</p>
                </div>
            </div>
        </header>
        <!-- Tombol untuk menambah produk -->
        <div class="container my-3">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">Tambah Produk</button>
        </div>

        <!-- Tabel Daftar Produk -->
        <div class="container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th></th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Deskripsi</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $produkResult->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['id_produk'] ?></td>
                            <td><?= $row['nama_produk'] ?></td>
                            <td><?= number_format($row['harga'], 2, ',', '.') ?></td>
                            <td><?= $row['deskripsi'] ?></td>
                            <td><?= $row['nama_kategori'] ?></td>
                            <td>
                                <!-- Tombol Edit -->
                                <a href="edit_produk.php?id=<?= $row['id_produk'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <!-- Tombol Hapus -->
                                <a href="hapus_produk.php?id=<?= $row['id_produk'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
                                
                                <!-- Tombol Pesan (Tambah ke Keranjang) -->
                                <form action="cart.php" method="POST" class="d-inline">
                                    <input type="hidden" name="produk_id" value="<?= $row['id_produk'] ?>">
                                    <input type="number" name="kuantitas" value="1" min="1" class="form-control" style="width: 60px; display: inline-block;" required>
                                    <button type="submit" name="tambah_ke_keranjang" class="btn btn-success btn-sm">Tambah ke Keranjang</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        
<!-- Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cartModalLabel">Keranjang Belanja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Isi keranjang belanja -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Kuantitas</th>
                            <th>Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data keranjang -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <!-- Tombol tutup modal -->
               <!-- Tombol untuk menutup halaman keranjang dan kembali ke halaman utama -->
                <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php'">Tutup</button>

                <a href="checkout.php" class="btn btn-primary">Checkout</a>
                <!-- Tombol Cancel Pesanan -->
                <form action="cart.php" method="POST" style="display: inline;">
                    <button type="submit" name="cancel_keranjang" class="btn btn-danger">Cancel Pesanan</button>
                </form>
            </div>
        </div>
    </div>
</div>



         <!-- Modal Tambah Produk -->
         <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductModalLabel">Tambah Produk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="index.php" method="POST">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nama_produk" class="form-label">Nama Produk</label>
                                <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
                            </div>
                            <div class="mb-3">
                                <label for="harga" class="form-label">Harga</label>
                                <input type="number" class="form-control" id="harga" name="harga" required>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="kategori_id" class="form-label">Kategori</label>
                                <select class="form-select" id="kategori_id" name="kategori_id" required>
                                    <?php while ($row = $kategoriResult->fetch_assoc()) { ?>
                                        <option value="<?= $row['id_kategori'] ?>"><?= $row['nama_kategori'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" name="tambah">Tambah</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
       

        <!-- Footer-->
        <footer class="py-1 bg-dark">
            <div class="container">
                <p class="m-0 text-center text-white">&copy; ThinkHappy 2024</p>
            </div>
            
        </footer>

        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>

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
