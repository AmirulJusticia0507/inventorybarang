<?php
include 'konekke_local.php';

// Periksa apakah pengguna telah terautentikasi
session_start();
if (!isset($_SESSION['userid'])) {
    // Jika tidak ada sesi pengguna, alihkan ke halaman login
    header('Location: login.php');
    exit;
}

// Proses form submit untuk menambah atau mengedit barang
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'edit') {
        // Proses edit barang
        $id = $_POST['id'];
        $nama_barang = $_POST['nama_barang'];
        $deskripsi = $_POST['deskripsi'];
        $harga = $_POST['harga'];
        $stok = $_POST['stok'];
        $klasifikasi_id = $_POST['klasifikasi_id'];

        $sql = "UPDATE barang SET nama_barang = ?, deskripsi = ?, harga = ?, stok = ?, klasifikasi_id = ? WHERE id = ?";
        $stmt = $koneklocalhost->prepare($sql);
        $stmt->bind_param("ssdiii", $nama_barang, $deskripsi, $harga, $stok, $klasifikasi_id, $id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['action']) && $_POST['action'] == 'delete') {
        // Proses delete barang
        $id = $_POST['id'];
        $sql = "DELETE FROM barang WHERE id = ?";
        $stmt = $koneklocalhost->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    } else {
        // Proses tambah barang
        $nama_barang = $_POST['nama_barang'];
        $deskripsi = $_POST['deskripsi'];
        $harga = $_POST['harga'];
        $stok = $_POST['stok'];
        $klasifikasi_id = $_POST['klasifikasi_id'];

        $sql = "INSERT INTO barang (nama_barang, deskripsi, harga, stok, klasifikasi_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $koneklocalhost->prepare($sql);
        $stmt->bind_param("ssdii", $nama_barang, $deskripsi, $harga, $stok, $klasifikasi_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Ambil data barang dari database
$sql_barang = "SELECT b.id, b.nama_barang, b.deskripsi, b.harga, b.stok, k.nama_klasifikasi 
               FROM barang b 
               JOIN klasifikasi_barang k ON b.klasifikasi_id = k.id";
$result_barang = $koneklocalhost->query($sql_barang);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Classification - Stocks Classifier</title>
    <!-- Tambahkan link Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tambahkan link AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/css/adminlte.min.css">
    <!-- Tambahkan link DataTables CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="checkbox.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <!-- Sertakan CSS Select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
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
                        <li class="breadcrumb-item active" aria-current="page">Classification</li>
                    </ol>
                </nav>
                <?php
                include 'navigation.php';
                ?>

                <div class="container-fluid mt-4">
                    <div class="card-body">
                        <h2>Tambah Klasifikasi Barang</h2>
                        <form method="POST" action="categories.php">
                            <div class="mb-3">
                                <label for="nama_klasifikasi" class="form-label">Nama Klasifikasi</label>
                                <input type="text" class="form-control" id="nama_klasifikasi" name="nama_klasifikasi" required>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Tambah Klasifikasi</button>
                        </form>
                    </div>
                </div><br><hr>

                <div class="container-fluid mt-4">
                    <h2>Daftar Barang</h2>
                    <table id="klasifikasiTable" class="display table table-bordered table-striped table-hover responsive nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Deskripsi</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Klasifikasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result_barang->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['nama_barang'] ?></td>
                                    <td><?= $row['deskripsi'] ?></td>
                                    <td><?= $row['harga'] ?></td>
                                    <td><?= $row['stok'] ?></td>
                                    <td><?= $row['nama_klasifikasi'] ?></td>
                                    <td>
                                        <button class="btn btn-warning" onclick="editBarang(<?= $row['id'] ?>, '<?= $row['nama_barang'] ?>', '<?= $row['deskripsi'] ?>', <?= $row['harga'] ?>, <?= $row['stok'] ?>, <?= $row['klasifikasi_id'] ?>)">Edit</button>
                                        <form method="POST" action="categories.php" style="display:inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Modal for Edit Product -->
                <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Barang</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form method="POST" action="categories.php">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="id" id="edit_id">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="edit_nama_barang">Nama Barang</label>
                                        <input type="text" class="form-control" id="edit_nama_barang" name="nama_barang" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_deskripsi">Deskripsi</label>
                                        <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_harga">Harga</label>
                                        <input type="number" class="form-control" id="edit_harga" name="harga" step="0.01" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_stok">Stok</label>
                                        <input type="number" class="form-control" id="edit_stok" name="stok" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_klasifikasi_id">Klasifikasi</label>
                                        <select class="form-control" id="edit_klasifikasi_id" name="klasifikasi_id" required>
                                            <?php
                                            $klasifikasi_result = $koneklocalhost->query("SELECT id, nama_klasifikasi FROM klasifikasi_barang");
                                            while ($row = $klasifikasi_result->fetch_assoc()):
                                            ?>
                                                <option value="<?= $row['id'] ?>"><?= $row['nama_klasifikasi'] ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </form>
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
            $('#klasifikasiTable').DataTable({
                responsive: true,
                scrollX: true,
                searching: true,
                lengthMenu: [10, 25, 50, 100, 500, 1000],
                pageLength: 10,
                dom: 'lBfrtip'
            });
        });

        function editBarang(id, nama, deskripsi, harga, stok, klasifikasi_id) {
            $('#edit_id').val(id);
            $('#edit_nama_barang').val(nama);
            $('#edit_deskripsi').val(deskripsi);
            $('#edit_harga').val(harga);
            $('#edit_stok').val(stok);
            $('#edit_klasifikasi_id').val(klasifikasi_id);
            $('#editModal').modal('show');
        }
    </script>
</body>
</html>