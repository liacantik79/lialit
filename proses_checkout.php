<?php
require 'config.php';

// Ambil data keranjang
$query = "SELECT keranjang.produk_id, keranjang.kuantitas, produk.harga 
          FROM keranjang 
          JOIN produk ON keranjang.produk_id = produk.id_produk";
$cartResult = $conn->query($query);

// Cek apakah ada data di keranjang
if ($cartResult->num_rows > 0) {
    // Mulai transaksi
    $conn->begin_transaction();

    try {
        while ($row = $cartResult->fetch_assoc()) {
            $id_produk = $row['produk_id'];
            $jumlah = $row['kuantitas'];
            $total_harga = $jumlah * $row['harga'];

            // Masukkan data ke tabel pesanan
            $queryInsert = "INSERT INTO pesanan (id_produk, jumlah, total_harga) 
                            VALUES ('$id_produk', '$jumlah', '$total_harga')";
            if (!$conn->query($queryInsert)) {
                throw new Exception("Gagal memasukkan data pesanan: " . $conn->error);
            }
        }

        // Hapus data dari tabel keranjang
        $queryDelete = "DELETE FROM keranjang";
        if (!$conn->query($queryDelete)) {
            throw new Exception("Gagal menghapus data keranjang: " . $conn->error);
        }

        // Commit transaksi
        $conn->commit();

        echo "<script>alert('Checkout berhasil!'); window.location = 'cart.php';</script>";
    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        $conn->rollback();
        echo "<script>alert('Checkout gagal: " . $e->getMessage() . "'); window.location = 'cart.php';</script>";
    }
} else {
    echo "<script>alert('Keranjang kosong!'); window.location = 'cart.php';</script>";
}
?>
