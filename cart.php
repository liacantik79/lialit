<?php
session_start();
require 'config.php';

// Proses menambahkan produk ke keranjang
if (isset($_POST['produk_id']) && isset($_POST['kuantitas'])) {
    $produk_id = $_POST['produk_id'];
    $kuantitas = $_POST['kuantitas'];
    $session_id = session_id(); // Menyimpan session_id

    // Cek apakah produk sudah ada di keranjang untuk sesi ini
    $checkQuery = "SELECT * FROM keranjang WHERE id_produk = ? AND session_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("is", $produk_id, $session_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika produk belum ada, tambahkan produk baru
    if ($result->num_rows == 0) {
        $insertQuery = "INSERT INTO keranjang (id_produk, kuantitas, session_id) VALUES (?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("iis", $produk_id, $kuantitas, $session_id);
        $insertStmt->execute();
    }

    // Redirect ke halaman keranjang setelah menambah produk
    header("Location: cart.php");
    exit();
}

// Cek apakah tombol 'Cancel Pesanan' ditekan
if (isset($_POST['cancel_keranjang'])) {
    // Kosongkan keranjang belanja di sesi
    unset($_SESSION['keranjang']);
    
    // Hapus semua produk dalam keranjang di database untuk session ini
    $session_id = session_id();
    $deleteQuery = "DELETE FROM keranjang WHERE session_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("s", $session_id);
    $stmt->execute();

    // Redirect setelah penghapusan
    header("Location: cart.php");
    exit();
}

// Ambil data produk di keranjang
$session_id = session_id();
$cartQuery = "SELECT produk.id_produk, produk.nama_produk, produk.harga, keranjang.kuantitas
              FROM keranjang
              JOIN produk ON keranjang.id_produk = produk.id_produk
              WHERE keranjang.session_id = ?";
$stmt = $conn->prepare($cartQuery);
$stmt->bind_param("s", $session_id);
$stmt->execute();
$cartResult = $stmt->get_result();
$totalHarga = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container mt-4">
        <h1>Keranjang Belanja</h1>
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
                <?php
                while ($row = $cartResult->fetch_assoc()) {
                    $totalProduk = $row['harga'] * $row['kuantitas'];
                    $totalHarga += $totalProduk;
                    echo "<tr>
                            <td>{$row['nama_produk']}</td>
                            <td>" . number_format($row['harga'], 2, ',', '.') . "</td>
                            <td>{$row['kuantitas']}</td>
                            <td>" . number_format($totalProduk, 2, ',', '.') . "</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="text-end">
            <h4>Total Harga: Rp <?= number_format($totalHarga, 2, ',', '.') ?></h4>

            <div class="modal-footer">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
