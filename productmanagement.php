<?php
include 'konekke_local.php';

// Periksa apakah pengguna telah terautentikasi
session_start();
if (!isset($_SESSION['userid'])) {
    // Jika tidak ada sesi pengguna, alihkan ke halaman login
    header('Location: login.php');
    exit;
}

// Proses form submit untuk tambah barang, edit barang, dan delete barang
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'add' || $action == 'edit') {
            // Inisialisasi variabel untuk semua aksi (add dan edit)
            $nama_barang = $_POST['nama_barang'];
            $deskripsi = $_POST['deskripsi'];
            $harga = $_POST['harga'];
            $stok = $_POST['stok'];
            $klasifikasi_id = $_POST['klasifikasi_id'];
            $photo_product = '';

            // Proses upload photo_product jika ada file baru yang diunggah
            if (isset($_FILES['photo_product']) && $_FILES['photo_product']['error'] == 0) {
                $target_dir = "uploads/products/";
                $photo_product = basename($_FILES['photo_product']['name']);
                $target_file = $target_dir . $photo_product;
                move_uploaded_file($_FILES['photo_product']['tmp_name'], $target_file);
            }

            if ($action == 'add') {
                // Proses tambah barang
                $sql = "INSERT INTO barang (nama_barang, deskripsi, harga, stok, klasifikasi_id, photo_product) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $koneklocalhost->prepare($sql);
                $stmt->bind_param("ssdiss", $nama_barang, $deskripsi, $harga, $stok, $klasifikasi_id, $photo_product);
                $success_message = "Barang berhasil ditambahkan.";
            } elseif ($action == 'edit' && isset($_POST['id'])) {
                // Proses edit barang
                $id = $_POST['id'];
                if (!empty($photo_product)) {
                    $sql = "UPDATE barang SET nama_barang = ?, deskripsi = ?, harga = ?, stok = ?, klasifikasi_id = ?, photo_product = ? WHERE id = ?";
                    $stmt = $koneklocalhost->prepare($sql);
                    $stmt->bind_param("ssdissi", $nama_barang, $deskripsi, $harga, $stok, $klasifikasi_id, $photo_product, $id);
                } else {
                    $sql = "UPDATE barang SET nama_barang = ?, deskripsi = ?, harga = ?, stok = ?, klasifikasi_id = ? WHERE id = ?";
                    $stmt = $koneklocalhost->prepare($sql);
                    $stmt->bind_param("ssdiii", $nama_barang, $deskripsi, $harga, $stok, $klasifikasi_id, $id);
                }
                $success_message = "Barang berhasil diperbarui.";
            }

            // Eksekusi statement SQL
            if ($stmt->execute()) {
                echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "Sukses!",
                            text: "' . $success_message . '",
                        }).then((result) => {
                            // Redirect to avoid form resubmission on refresh
                            window.location.href = "productmanagement.php";
                        });
                      </script>';
            } else {
                echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Terjadi kesalahan. Silakan coba lagi nanti.",
                        });
                      </script>';
            }
            $stmt->close();
        } elseif ($action == 'delete' && isset($_POST['id'])) {
            // Proses hapus barang
            $id = $_POST['id'];
            $sql = "DELETE FROM barang WHERE id = ?";
            $stmt = $koneklocalhost->prepare($sql);
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "Sukses!",
                            text: "Barang berhasil dihapus.",
                        }).then((result) => {
                            // Redirect to avoid form resubmission on refresh
                            window.location.href = "productmanagement.php";
                        });
                      </script>';
            } else {
                echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Terjadi kesalahan. Silakan coba lagi nanti.",
                        });
                      </script>';
            }
            $stmt->close();
        }
    }
}

// Ambil data klasifikasi barang
$klasifikasi_sql = "SELECT id, nama_klasifikasi FROM klasifikasi_barang";
$klasifikasi_result = $koneklocalhost->query($klasifikasi_sql);

// Ambil data barang
$barang_sql = "SELECT barang.id, barang.nama_barang, barang.deskripsi, barang.harga, barang.stok, klasifikasi_barang.nama_klasifikasi 
               FROM barang 
               JOIN klasifikasi_barang ON barang.klasifikasi_id = klasifikasi_barang.id";
$barang_result = $koneklocalhost->query($barang_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Products - Stocks Classifier</title>
    <!-- Tambahkan link Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tambahkan link AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/css/adminlte.min.css">
    <!-- Tambahkan link DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <!-- Tambahkan link DataTables CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="checkbox.css">
    <!-- Sertakan CSS Select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<!-- <link rel="stylesheet" href="uploadfoto.css"> -->
    <link rel="icon" href="img/stockclassifier1.png" type="image/png">
    <style>
        /* Tambahkan CSS agar tombol accordion terlihat dengan baik */
        .btn-link {
            text-decoration: none;
            color: #007bff; /* Warna teks tombol */
        }

        .btn-link:hover {
            text-decoration: underline;
        }

        .card-header {
            background-color: #f7f7f7; /* Warna latar belakang header card */
        }

        #notification {
            display: none;
            margin-top: 10px; /* Adjust this value based on your layout */
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #f8f8f8;
            color: #333;
        }
    </style>
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
                        <li class="breadcrumb-item active" aria-current="page">Products</li>
                    </ol>
                </nav>
                <?php
                include 'navigation.php';
                ?>

                <div class="container-fluid">
                    <div class="card-body">
                        <h2>Tambah/Edit Barang</h2>
                        <form method="POST" action="productmanagement.php" enctype="multipart/form-data">
                            <input type="hidden" name="action" id="action">
                            <input type="hidden" name="id" id="editId">
                            <div class="mb-3">
                                <label for="nama_barang" class="form-label"><i class="fas fa-box"></i> Nama Barang</label>
                                <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="<?php echo isset($nama_barang) ? htmlspecialchars($nama_barang) : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label"><i class="fas fa-info-circle"></i> Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?php echo isset($deskripsi) ? htmlspecialchars($deskripsi) : ''; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="harga" class="form-label"><i class="fas fa-dollar-sign"></i> Harga</label>
                                <input type="number" class="form-control" id="harga" name="harga" step="0.01" value="<?php echo isset($harga) ? $harga : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="stok" class="form-label"><i class="fas fa-layer-group"></i> Stok</label>
                                <input type="number" class="form-control" id="stok" name="stok" value="<?php echo isset($stok) ? $stok : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="klasifikasi_id" class="form-label"><i class="fas fa-list"></i> Klasifikasi</label>
                                <select class="form-control" id="klasifikasi_id" name="klasifikasi_id" required>
                                    <?php while ($row = $klasifikasi_result->fetch_assoc()): ?>
                                        <option value="<?= $row['id'] ?>" <?php echo isset($klasifikasi_id) && $klasifikasi_id == $row['id'] ? 'selected' : ''; ?>>
                                            <?= $row['nama_klasifikasi'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="photo_product" class="form-label"><i class="fas fa-camera"></i> Photo Product</label>
                                <input type="file" class="form-control-file" id="photo_product" name="photo_product">
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                    <hr>
                    <div class="card-body mt-5">
                        <h2>Data Barang</h2>
                        <table id="barangTable" class="display table table-bordered table-striped table-hover responsive nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Barang</th>
                                    <th>Deskripsi</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Klasifikasi</th>
                                    <th nowrap>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $barang_result->fetch_assoc()): ?>
                                    <tr id="row_<?= $row['id'] ?>">
                                        <td><?= $row['id'] ?></td>
                                        <td><?= $row['nama_barang'] ?></td>
                                        <td><?= $row['deskripsi'] ?></td>
                                        <td><?= number_format($row['harga'], 2) ?></td>
                                        <td><?= $row['stok'] ?></td>
                                        <td><?= $row['nama_klasifikasi'] ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning editBtn" data-id="<?= $row['id'] ?>"><i class="fas fa-edit"></i> Edit</button>
                                            <button type="button" class="btn btn-sm btn-danger deleteBtn" data-id="<?= $row['id'] ?>"><i class="fas fa-trash-alt"></i> Hapus</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal for Product Details -->
                <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="detailsModalLabel">Detail Barang</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Nama Barang:</strong> <span id="details_nama_barang"></span></p>
                                <p><strong>Deskripsi:</strong> <span id="details_deskripsi"></span></p>
                                <p><strong>Harga:</strong> <span id="details_harga"></span></p>
                                <p><strong>Stok:</strong> <span id="details_stok"></span></p>
                                <p><strong>Klasifikasi:</strong> <span id="details_klasifikasi"></span></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>
<?php include 'footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/js/adminlte.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<!-- Sertakan DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<!-- Tambahkan Select2 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
    <script>
        $(document).ready(function() {
            // Tambahkan event click pada tombol pushmenu
            $('.nav-link[data-widget="pushmenu"]').on('click', function() {
                // Toggle class 'sidebar-collapse' pada elemen body
                $('body').toggleClass('sidebar-collapse');
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#barangTable').DataTable({
                responsive: true,
                scrollX: true,
                searching: true,
                lengthMenu: [10, 25, 50, 100, 500, 1000],
                pageLength: 10,
                dom: 'lBfrtip'
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Script untuk tombol edit
            $(".editBtn").on('click', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: 'fetch_product.php',
                    method: 'POST',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#editId').val(data.id);
                        $('#nama_barang').val(data.nama_barang);
                        $('#deskripsi').val(data.deskripsi);
                        $('#harga').val(data.harga);
                        $('#stok').val(data.stok);
                        $('#klasifikasi_id').val(data.klasifikasi_id);
                        $('#action').val('edit');
                    }
                });
            });

            // Script untuk tombol hapus
            $(".deleteBtn").on('click', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda tidak akan dapat mengembalikan ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'delete_product.php',
                            method: 'POST',
                            data: {
                                id: id
                            },
                            success: function(data) {
                                Swal.fire(
                                    'Deleted!',
                                    'Barang berhasil dihapus.',
                                    'success'
                                ).then((result) => {
                                    window.location.reload();
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>