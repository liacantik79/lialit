<?php
session_start();
require 'config.php';

// Cek apakah keranjang ada untuk sesi ini
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

// Jika produk keranjang kosong, beri pesan
if ($cartResult->num_rows == 0) {
    echo "Keranjang Belanja Anda kosong.";
    exit();
}

// Proses checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Simpan data checkout ke dalam database pesanan (orders)
    $user_id = 1;  // Anda bisa mengubahnya dengan ID pengguna yang login
    $status = 'Pending';  // Status pesanan

    // Insert order ke tabel orders
    $orderQuery = "INSERT INTO orders (user_id, total_harga, status) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($orderQuery);
    $stmt->bind_param("iis", $user_id, $totalHarga, $status);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Simpan produk dalam pesanan
    $cartQuery = "SELECT id_produk, kuantitas FROM keranjang WHERE session_id = ?";
    $stmt = $conn->prepare($cartQuery);
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $cartResult = $stmt->get_result();

    while ($row = $cartResult->fetch_assoc()) {
        $insertOrderDetailQuery = "INSERT INTO order_details (order_id, id_produk, kuantitas) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertOrderDetailQuery);
        $stmt->bind_param("iii", $order_id, $row['id_produk'], $row['kuantitas']);
        $stmt->execute();
    }

    // Hapus produk dari keranjang setelah checkout
    $deleteQuery = "DELETE FROM keranjang WHERE session_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("s", $session_id);
    $stmt->execute();

    // Redirect ke halaman konfirmasi atau pembayaran
    header("Location: confirmation.php?order_id=" . $order_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container mt-4">
        <h1>Checkout</h1>
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

        <h4>Total Harga: Rp <?= number_format($totalHarga, 2, ',', '.') ?></h4>
        <!-- Tombol untuk kembali ke halaman utama (Tutup) -->
        <button type="button" class="btn btn-secondary mt-3" onclick="window.location.href='index.php'">Tutup</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
