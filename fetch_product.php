<?php
include 'konekke_local.php';

// Periksa apakah ada request POST dan parameter ID yang diterima
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Query untuk mengambil data barang berdasarkan ID
    $sql = "SELECT * FROM barang WHERE id = ?";
    $stmt = $koneklocalhost->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika data ditemukan, kirim data dalam format JSON
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Data barang tidak ditemukan']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid Request']);
}
?>
