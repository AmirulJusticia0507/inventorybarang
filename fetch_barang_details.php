<?php
include 'konekke_local.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $barang_id = $_POST['barang_id'];

    $sql = "SELECT nama_barang, harga FROM barang WHERE id = ?";
    $stmt = $koneklocalhost->prepare($sql);
    $stmt->bind_param("i", $barang_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response = [
            'nama_barang' => $row['nama_barang'],
            'harga_per_unit' => $row['harga']
        ];
        echo json_encode($response);
    } else {
        echo json_encode(['error' => 'Barang tidak ditemukan']);
    }

    $stmt->close();
}
?>
