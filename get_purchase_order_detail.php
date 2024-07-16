<?php
include 'konekke_local.php';

// Periksa apakah pengguna telah terautentikasi
session_start();
if (!isset($_SESSION['userid'])) {
    // Jika tidak ada sesi pengguna, alihkan ke halaman login
    header('Location: login.php');
    exit;
}

// Pastikan ID yang dikirimkan melalui POST tersedia
if (!isset($_POST['id'])) {
    echo json_encode(array("status" => "error", "message" => "ID detail purchase order tidak tersedia!"));
    exit;
}

$id = $_POST['id'];

// Query untuk mengambil detail purchase order berdasarkan ID
$sql = "SELECT pod.id, pod.purchase_order_id, pod.barang_id, pod.quantity, pod.harga_per_unit, b.photo_product, b.nama_barang
        FROM purchase_order_details pod
        LEFT JOIN barang b ON pod.barang_id = b.id
        WHERE pod.id = ?";
$stmt = $koneklocalhost->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

// Pastikan query berhasil dieksekusi
if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $purchase_order_id, $barang_id, $quantity, $harga_per_unit, $photo_product, $nama_barang);
    $stmt->fetch();

    // Format hasil ke dalam array JSON
    $response = array(
        "id" => $id,
        "purchase_order_id" => $purchase_order_id,
        "barang_id" => $barang_id,
        "quantity" => $quantity,
        "harga_per_unit" => $harga_per_unit,
        "photo_product" => $photo_product,
        "nama_barang" => $nama_barang
    );

    echo json_encode($response);
} else {
    echo json_encode(array("status" => "error", "message" => "Detail purchase order tidak ditemukan!"));
}

$stmt->close();
?>
