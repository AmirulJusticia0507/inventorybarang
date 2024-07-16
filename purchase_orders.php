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

// Proses form submit untuk menambah, mengedit, atau menghapus purchase order
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($action == 'edit') {
        // Proses edit purchase order
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $supplier_id = isset($_POST['supplier_id']) ? $_POST['supplier_id'] : '';
        $tanggal_pesanan = isset($_POST['tanggal_pesanan']) ? $_POST['tanggal_pesanan'] : '';
        $status = isset($_POST['status']) ? $_POST['status'] : '';

        $sql = "UPDATE purchase_orders SET supplier_id = ?, tanggal_pesanan = ?, status = ? WHERE purchase_order_id = ?";
        $stmt = $koneklocalhost->prepare($sql);
        $stmt->bind_param("issi", $supplier_id, $tanggal_pesanan, $status, $id);
        if ($stmt->execute()) {
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Purchase order berhasil diupdate!',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location = 'purchase_orders.php';
                        }
                    });
                  </script>";
        } else {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan saat mengupdate purchase order!',
                    });
                  </script>";
        }
        $stmt->close();
    } elseif ($action == 'delete') {
        // Proses delete purchase order
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $sql = "DELETE FROM purchase_orders WHERE purchase_order_id = ?";
        $stmt = $koneklocalhost->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Purchase order berhasil dihapus!',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location = 'purchase_orders.php';
                        }
                    });
                  </script>";
        } else {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan saat menghapus purchase order!',
                    });
                  </script>";
        }
        $stmt->close();
    } else {
        // Proses tambah purchase order
        $supplier_id = isset($_POST['supplier_id']) ? $_POST['supplier_id'] : '';
        $tanggal_pesanan = isset($_POST['tanggal_pesanan']) ? $_POST['tanggal_pesanan'] : '';
        $status = isset($_POST['status']) ? $_POST['status'] : '';

        // Generate kode unik
        $purchase_order_id = generateUniqueCode(11);

        $sql = "INSERT INTO purchase_orders (purchase_order_id, supplier_id, tanggal_pesanan, status) VALUES (?, ?, ?, ?)";
        $stmt = $koneklocalhost->prepare($sql);
        $stmt->bind_param("ssss", $purchase_order_id, $supplier_id, $tanggal_pesanan, $status);
        if ($stmt->execute()) {
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Purchase order berhasil ditambahkan!',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location = 'purchase_orders.php';
                        }
                    });
                </script>";
        } else {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan saat menambah purchase order!',
                    });
                </script>";
        }
        $stmt->close();
    }
}

function generateUniqueCode($length = 11) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }

    return $randomString;
}

// Contoh penggunaan
$new_purchase_order_id = generateUniqueCode();

// Ambil data purchase orders dari database
$sql_purchase_orders = "SELECT 
po.purchase_order_id, 
po.supplier_id, 
s.nama_supplier, 
po.tanggal_pesanan, 
po.status 
FROM purchase_orders po 
JOIN supplier s ON po.supplier_id = s.id
ORDER BY po.tanggal_pesanan DESC";
$result_purchase_orders = $koneklocalhost->query($sql_purchase_orders);

// Ambil data supplier untuk dropdown
$sql_supplier = "SELECT id, nama_supplier FROM supplier";
$result_supplier = $koneklocalhost->query($sql_supplier);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Purchase Order Management</title>
    <!-- Tambahkan link Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tambahkan link AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/css/adminlte.min.css">
    <!-- Tambahkan link DataTables CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="checkbox.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.15/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <!-- Sertakan CSS Select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="icon" href="img/stockclassifier1.png" type="image/png">
    <style>
        .myButtonCekSaldo {
            box-shadow: 3px 4px 0px 0px #899599;
            background:linear-gradient(to bottom, #ededed 5%, #bab1ba 100%);
            background-color:#ededed;
            border-radius:15px;
            border:1px solid #d6bcd6;
            display:inline-block;
            cursor:pointer;
            color:#3a8a9e;
            font-family:Arial;
            font-size:17px;
            padding:7px 25px;
            text-decoration:none;
            text-shadow:0px 1px 0px #e1e2ed;
        }
        .myButtonCekSaldo:hover {
            background:linear-gradient(to bottom, #bab1ba 5%, #ededed 100%);
            background-color:#bab1ba;
        }
        .myButtonCekSaldo:active {
            position:relative;
            top:1px;
        }

        #imagePreview img {
            margin-right: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            padding: 5px;
            height: 150px;
        }

        .preview-card {
            border: 1px solid #dee2e6;
            padding: 1rem;
            border-radius: .25rem;
            background-color: #f8f9fa;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
            <?php include 'header.php'; ?>
        </nav>
        
        <?php include 'sidebar.php'; ?>

        <div class="content-wrapper">
            <!-- Konten Utama -->
            <main class="content">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Purchase Order Management</li>
                    </ol>
                </nav>
                <?php
                include 'navigation.php';
                ?>

                <div class="container-fluid mt-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h2>Form Purchase Orders</h2>
                                    <form method="POST" action="purchase_orders.php">
                                        <input type="hidden" id="action" name="action" value="add">
                                        <input type="hidden" id="id" name="id" value="">
                                        <div class="mb-3">
                                            <label for="supplier_id" class="form-label"><i class="fas fa-industry"></i> Supplier</label>
                                            <div>
                                                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#supplierModal"><i class="fas fa-plus"></i> Pilih Supplier</button>
                                            </div>
                                            <div id="selected_suppliers" class="mt-3"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="tanggal_pesanan" class="form-label"><i class="fas fa-calendar-alt"></i> Tanggal Pesanan</label>
                                            <input type="date" class="form-control" id="tanggal_pesanan" name="tanggal_pesanan" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="status" class="form-label"><i class="fas fa-info-circle"></i> Status</label>
                                            <select class="form-control" id="status" name="status" required>
                                                <option value="Menunggu Konfirmasi" <?php echo isset($_POST['status']) && $_POST['status'] == 'Menunggu Konfirmasi' ? 'selected' : ''; ?>>Menunggu Konfirmasi</option>
                                                <option value="Dalam Proses" <?php echo isset($_POST['status']) && $_POST['status'] == 'Dalam Proses' ? 'selected' : ''; ?>>Dalam Proses</option>
                                                <option value="Telah Dikirim" <?php echo isset($_POST['status']) && $_POST['status'] == 'Telah Dikirim' ? 'selected' : ''; ?>>Telah Dikirim</option>
                                                <option value="Selesai" <?php echo isset($_POST['status']) && $_POST['status'] == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                                            </select>
                                        </div>
                                        <button type="submit" id="submitBtn" class="myButtonCekSaldo"><i class="fas fa-file-invoice"></i> Tambah Purchase Order</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card preview-card">
                                <div class="card-body">
                                    <h2>Detail Purchase Orders</h2>
                                    <div id="preview">
                                        <!-- Preview detail akan ditampilkan di sini -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-4">
                        <div class="card-body">
                            <table id="purchaseDatatable" class="display table table-bordered table-striped table-hover responsive nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Supplier</th>
                                        <th>Tanggal Pesanan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    while ($row = $result_purchase_orders->fetch_assoc()) {
                                    ?>
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo $row['nama_supplier']; ?></td>
                                            <td><?php echo date('d-m-Y', strtotime($row['tanggal_pesanan'])); ?></td>
                                            <td><?php echo $row['status']; ?></td>
                                            <td>
                                                <button class="btn btn-warning btn-sm btn-edit" data-id="<?php echo $row['purchase_order_id']; ?>" data-supplier_id="<?php echo $row['supplier_id']; ?>" data-tanggal_pesanan="<?php echo $row['tanggal_pesanan']; ?>" data-status="<?php echo $row['status']; ?>"><i class="fas fa-edit"></i> Edit</button>
                                                <button class="btn btn-danger btn-sm btn-delete" data-id="<?php echo $row['purchase_order_id']; ?>"><i class="fas fa-trash-alt"></i> Delete</button>
                                            </td>
                                        </tr>
                                    <?php
                                        $no++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Modal -->
                    <div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="supplierModalLabel">Pilih Supplier</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table id="supplierTable" class="display table table-bordered table-striped table-hover responsive nowrap" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th style="width:auto;">Nama Supplier</th>
                                                <th style="width:auto;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Sertakan file koneksi ke database
                                            include 'konekke_local.php';
                                            
                                            // Ambil data supplier untuk tabel
                                            $sql_supplier = "SELECT id, nama_supplier FROM supplier";
                                            $result_supplier = $koneklocalhost->query($sql_supplier);
                                            
                                            while ($row_supplier = $result_supplier->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $row_supplier['nama_supplier'] ?></td>
                                                    <td>
                                                        <button class="btn btn-link select-supplier" data-id="<?= $row_supplier['id'] ?>" data-name="<?= $row_supplier['nama_supplier'] ?>">Pilih</button>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="notification"></div>

            </main>
        </div>
        <?php include 'footer.php'; ?>
    </div>

    <!-- Tambahkan script JavaScript untuk Bootstrap, AdminLTE, dan DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/js/adminlte.min.js"></script>
    <!-- Sertakan DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.15/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Sertakan JavaScript Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables
            $('#purchaseDatatable, #supplierTable').DataTable({
                responsive: true,
                scrollX: true,
                searching: true,
                lengthMenu: [10, 25, 50, 100, 500, 1000],
                pageLength: 10,
                dom: 'lBfrtip'
            });

            // Inisialisasi Select2
            $('#supplier_id').select2();

            // Event handler untuk tombol edit
            $('.btn-edit').click(function() {
                // Ambil data dari tombol edit yang diklik
                var id = $(this).data('id');
                var supplier_id = $(this).data('supplier_id');
                var tanggal_pesanan = $(this).data('tanggal_pesanan');
                var status = $(this).data('status');

                // Masukkan data ke dalam form untuk diedit
                $('#id').val(id);
                $('#supplier_id').val(supplier_id).trigger('change');
                $('#tanggal_pesanan').val(tanggal_pesanan);
                $('#status').val(status);
                $('#action').val('edit');
                $('#submitBtn').text('Update Purchase Order');
                updatePreview();
            });

            // Event handler untuk tombol delete
            $('.btn-delete').click(function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Anda yakin?',
                    text: "Purchase order akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('<form method="POST" action="purchase_orders.php">')
                            .append($('<input type="hidden" name="id" value="' + id + '">'))
                            .append($('<input type="hidden" name="action" value="delete">'))
                            .appendTo('body')
                            .submit();
                    }
                });
            });

            // Reset form saat modal ditutup
            $('#addSupplierModal').on('hidden.bs.modal', function() {
                $('#id').val('');
                $('#supplier_id').val('').trigger('change');
                $('#tanggal_pesanan').val('');
                $('#status').val('');
                $('#action').val('add');
                $('#submitBtn').text('Tambah Purchase Order');
                updatePreview();
            });

            // Event handler untuk update preview detail
            $('input, select').on('input change', function() {
                updatePreview();
            });

            // Function untuk update preview detail
            function updatePreview() {
                var supplier_id = $('#supplier_id option:selected').text();
                var tanggal_pesanan = $('#tanggal_pesanan').val();
                var status = $('#status').val();

                // Ambil tanggal dari PHP
                var tanggal_pesanan_php = "<?php echo $row['tanggal_pesanan']; ?>";

                // Buat objek Date dari tanggal PHP
                var tanggal_pesanan = new Date(tanggal_pesanan_php);

                // Buat fungsi untuk format tanggal dalam format d-m-Y
                function formatDate(date) {
                    var day = date.getDate();
                    var month = date.getMonth() + 1;
                    var year = date.getFullYear();

                    return day + '-' + month + '-' + year;
                }

                // Format tanggal_pesanan dalam format d-m-Y
                var formatted_tanggal_pesanan = formatDate(tanggal_pesanan);

                $('#preview').html(`
                    <p><strong>Supplier:</strong> ${supplier_id}</p>
                    <p><strong>Tanggal Pesanan:</strong> ${formatted_tanggal_pesanan}</p>
                    <p><strong>Status:</strong> ${status}</p>
                `);
            }

            // Fungsi untuk menambahkan supplier yang dipilih ke input form
            $(document).on('click', '.select-supplier', function() {
                var supplierId = $(this).data('id');
                var supplierName = $(this).data('name');

                // Tambahkan supplier yang dipilih ke input hidden
                if (!$('#selected_suppliers').find('input[value="' + supplierId + '"]').length) {
                    $('#selected_suppliers').append('<input type="hidden" name="supplier_id" value="' + supplierId + '">' +
                                                    '<span class="badge bg-primary mx-1">' + supplierName + ' <a href="#" class="remove-supplier" data-id="' + supplierId + '">&times;</a></span>');
                }

                // Tutup modal
                $('#supplierModal').modal('hide');
                updatePreview();
            });

            // Fungsi untuk menghapus supplier yang dipilih
            $(document).on('click', '.remove-supplier', function(e) {
                e.preventDefault();
                var supplierId = $(this).data('id');
                $(this).parent().remove();
                $('#selected_suppliers').find('input[value="' + supplierId + '"]').remove();
                updatePreview();
            });
        });
    </script>

</body>
</html>
