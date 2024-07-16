<?php
include 'konekke_local.php';

// Periksa apakah pengguna telah terautentikasi
session_start();
if (!isset($_SESSION['userid'])) {
    // Jika tidak ada sesi pengguna, alihkan ke halaman login
    header('Location: login.php');
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

// Proses form submit untuk menambah, mengedit, atau menghapus detail purchase order
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($action == 'edit') {
        // Proses edit detail purchase order
        $id = $_POST['id'];
        $purchase_order_id = $_POST['purchase_order_id'];
        $barang_id = $_POST['barang_id'];
        $quantity = $_POST['quantity'];
        $harga_per_unit = $_POST['harga_per_unit'];

        // Check if purchase_order_id exists in purchase_orders table
        $check_sql = "SELECT purchase_order_id FROM purchase_orders WHERE purchase_order_id = ?";
        $check_stmt = $koneklocalhost->prepare($check_sql);
        $check_stmt->bind_param("i", $purchase_order_id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            // Update statement
            $sql = "UPDATE purchase_order_details SET purchase_order_id = ?, barang_id = ?, quantity = ?, harga_per_unit = ? WHERE id = ?";
            $stmt = $koneklocalhost->prepare($sql);
            $stmt->bind_param("siidi", $purchase_order_id, $barang_id, $quantity, $harga_per_unit, $id);
            if ($stmt->execute()) {
                echo json_encode(array("status" => "success", "message" => "Detail purchase order berhasil diupdate!"));
            } else {
                echo json_encode(array("status" => "error", "message" => "Terjadi kesalahan saat mengupdate detail purchase order: " . $stmt->error));
            }
            $stmt->close();
        } else {
            echo json_encode(array("status" => "error", "message" => "Purchase Order ID tidak valid!"));
        }

        $check_stmt->close();
    } 
    elseif ($action == 'delete') {
        // Proses delete detail purchase order
        $id = $_POST['id'];
        
        // Ambil barang_id dari detail yang akan dihapus
        $sql_select_barang = "SELECT barang_id, quantity FROM purchase_order_details WHERE id = ?";
        $stmt_select_barang = $koneklocalhost->prepare($sql_select_barang);
        $stmt_select_barang->bind_param("i", $id);
        $stmt_select_barang->execute();
        $stmt_select_barang->bind_result($barang_id, $quantity);
        $stmt_select_barang->fetch();
        $stmt_select_barang->close();
        
        // Hapus detail purchase order
        $sql_delete_detail = "DELETE FROM purchase_order_details WHERE id = ?";
        $stmt_delete_detail = $koneklocalhost->prepare($sql_delete_detail);
        $stmt_delete_detail->bind_param("i", $id);
        
        if ($stmt_delete_detail->execute()) {
            echo json_encode(array("status" => "success", "message" => "Detail purchase order berhasil dihapus!"));
            
            // Kembalikan stok barang
            $sql_update_stok = "UPDATE barang SET stok = stok + ? WHERE id = ?";
            $stmt_update_stok = $koneklocalhost->prepare($sql_update_stok);
            $stmt_update_stok->bind_param("ii", $quantity, $barang_id);
            $stmt_update_stok->execute();
            $stmt_update_stok->close();
        } else {
            echo json_encode(array("status" => "error", "message" => "Terjadi kesalahan saat menghapus detail purchase order: " . $stmt_delete_detail->error));
        }
        $stmt_delete_detail->close();
    }
    
    elseif ($action == 'add') {
        // Proses tambah detail purchase order
        $purchase_order_id = $_POST['purchase_order_id'];
        $barang_id = $_POST['barang_id'];
        $quantity = $_POST['quantity'];
        $harga_per_unit = $_POST['harga_per_unit'];

        // Check if purchase_order_id exists in purchase_orders table
        $check_sql = "SELECT purchase_order_id FROM purchase_orders WHERE purchase_order_id = ?";
        $check_stmt = $koneklocalhost->prepare($check_sql);
        $check_stmt->bind_param("s", $purchase_order_id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            // Insert statement
            $sql = "INSERT INTO purchase_order_details (purchase_order_id, barang_id, quantity, harga_per_unit) VALUES (?, ?, ?, ?)";
            $stmt = $koneklocalhost->prepare($sql);
            $stmt->bind_param("siid", $purchase_order_id, $barang_id, $quantity, $harga_per_unit);
            if ($stmt->execute()) {
                echo json_encode(array("status" => "success", "message" => "Detail purchase order berhasil ditambahkan!"));
                // Tampilkan SweetAlert
                echo '<script>showSuccessMessage("Detail purchase order berhasil ditambahkan!");</script>';
            } else {
                echo json_encode(array("status" => "error", "message" => "Terjadi kesalahan saat menambah detail purchase order: " . $stmt->error));
            }
            $stmt->close();
        } else {
            echo json_encode(array("status" => "error", "message" => "Purchase Order ID tidak valid!"));
        }

        $check_stmt->close();
    }
}
?>
