<?php
require 'config.php';

if (isset($_GET['id'])) {
    $id_produk = $_GET['id'];

    $query = "DELETE FROM produk WHERE id_produk = $id_produk";
    if ($conn->query($query) === TRUE) {
        header('Location: index.php');
        exit();
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}
?>
