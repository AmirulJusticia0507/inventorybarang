<?php
include 'konekke_local.php';

// Periksa apakah ada request POST dan parameter ID yang diterima
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Query untuk menghapus barang berdasarkan ID
    $sql = "DELETE FROM barang WHERE id = ?";
    $stmt = $koneklocalhost->prepare($sql);
    $stmt->bind_param("i", $id);

    // Eksekusi query penghapusan
    if ($stmt->execute()) {
        echo "Barang berhasil dihapus.";
    } else {
        echo "Gagal menghapus barang.";
    }

    $stmt->close();
} else {
    echo "Invalid Request";
}
?>
