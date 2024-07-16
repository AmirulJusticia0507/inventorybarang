<?php
include 'konekke_local.php'; // Sesuaikan dengan file koneksi database Anda

// Ambil nilai input teks dari parameter 'term'
$term = $_GET['term'];

// Query untuk mencari supplier berdasarkan nama_supplier yang cocok dengan nilai 'term'
$query = "SELECT id, nama_supplier FROM supplier WHERE nama_supplier LIKE ?";
$stmt = $koneklocalhost->prepare($query);
$searchTerm = "%$term%"; // Tambahkan wildcard % ke depan dan belakang untuk pencarian
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

// Siapkan array untuk menyimpan hasil pencarian
$suppliers = array();
while ($row = $result->fetch_assoc()) {
    $supplier = array(
        'id' => $row['id'],
        'text' => $row['nama_supplier'] // Gunakan 'text' untuk menyesuaikan dengan Select2
    );
    array_push($suppliers, $supplier);
}

// Kembalikan hasil dalam format JSON
echo json_encode($suppliers);

// Tutup statement dan koneksi database
$stmt->close();
$koneklocalhost->close();
?>
