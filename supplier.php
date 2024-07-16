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

// Proses form submit untuk menambah, mengedit, atau menghapus supplier
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($action == 'edit') {
        // Proses edit supplier
        $id = $_POST['id'];
        $nama_supplier = $_POST['nama_supplier'];
        $alamat = $_POST['alamat'];
        $kontak = $_POST['kontak'];

        $sql = "UPDATE supplier SET nama_supplier = ?, alamat = ?, kontak = ? WHERE id = ?";
        $stmt = $koneklocalhost->prepare($sql);
        $stmt->bind_param("sssi", $nama_supplier, $alamat, $kontak, $id);
        if ($stmt->execute()) {
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Supplier berhasil diupdate!',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location = 'supplier.php';
                        }
                    });
                  </script>";
        } else {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan saat mengupdate supplier!',
                    });
                  </script>";
        }
        $stmt->close();
    } elseif ($action == 'delete') {
        // Proses delete supplier
        $id = $_POST['id'];
        $sql = "DELETE FROM supplier WHERE id = ?";
        $stmt = $koneklocalhost->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Supplier berhasil dihapus!',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location = 'supplier.php';
                        }
                    });
                  </script>";
        } else {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan saat menghapus supplier!',
                    });
                  </script>";
        }
        $stmt->close();
    } else {
        // Proses tambah supplier
        $nama_supplier = $_POST['nama_supplier'];
        $alamat = $_POST['alamat'];
        $kontak = $_POST['kontak'];

        $sql = "INSERT INTO supplier (nama_supplier, alamat, kontak) VALUES (?, ?, ?)";
        $stmt = $koneklocalhost->prepare($sql);
        $stmt->bind_param("sss", $nama_supplier, $alamat, $kontak);
        if ($stmt->execute()) {
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Supplier berhasil ditambahkan!',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location = 'supplier.php';
                        }
                    });
                  </script>";
        } else {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan saat menambah supplier!',
                    });
                  </script>";
        }
        $stmt->close();
    }
}

// Ambil data supplier dari database
$sql_supplier = "SELECT id, nama_supplier, alamat, kontak FROM supplier";
$result_supplier = $koneklocalhost->query($sql_supplier);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Supplier Management</title>
    <!-- Tambahkan link Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <li class="breadcrumb-item active" aria-current="page">Supplier Management</li>
                    </ol>
                </nav>
                <?php
                include 'navigation.php';
                ?>

                <div class="container-fluid mt-4">
                    <div class="card-body">
                        <h2>Form Supplier</h2>
                        <form method="POST" action="supplier.php">
                            <input type="hidden" id="action" name="action" value="add">
                            <input type="hidden" id="id" name="id" value="">

                            <div class="mb-3">
                                <label for="nama_supplier" class="form-label"><i class="fas fa-tags"></i> Nama Supplier</label>
                                <input type="text" class="form-control" id="nama_supplier" name="nama_supplier" required>
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label"><i class="fas fa-info-circle"></i> Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="kontak" class="form-label"><i class="fas fa-phone"></i> Telepon</label>
                                <input type="text" class="form-control" id="kontak" name="kontak" required>
                            </div>
                            <button type="submit" id="submitBtn" class="myButtonCekSaldo"><i class="fas fa-truck"></i> Tambah Supplier</button>
                        </form>
                    </div>
                </div>
                <div class="container-fluid mt-4">
                    <div class="card">
                        <div class="card-body">
                            <table id="supplierDatatable" class="display table table-bordered table-striped table-hover responsive nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Supplier</th>
                                        <th>Alamat</th>
                                        <th>Telepon</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    while ($row = $result_supplier->fetch_assoc()) {
                                    ?>
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo $row['nama_supplier']; ?></td>
                                            <td><?php echo $row['alamat']; ?></td>
                                            <td><?php echo $row['kontak']; ?></td>
                                            <td>
                                                <button class="btn btn-warning btn-sm btn-edit" data-id="<?php echo $row['id']; ?>" data-nama_supplier="<?php echo $row['nama_supplier']; ?>" data-alamat="<?php echo $row['alamat']; ?>" data-kontak="<?php echo $row['kontak']; ?>"><i class="fas fa-edit"></i> Edit</button>
                                                <button class="btn btn-danger btn-sm btn-delete" data-id="<?php echo $row['id']; ?>"><i class="fas fa-trash-alt"></i> Delete</button>
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
                </div>

                <div id="notification"></div>

            
            </main>
        </div>
    </div>
<?php include 'footer.php'; ?>
    <!-- Tambahkan script JavaScript untuk Bootstrap, AdminLTE, dan DataTables -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/js/adminlte.min.js"></script>
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
            $('#supplierDatatable').DataTable({
                responsive: true,
                scrollX: true,
                searching: true,
                lengthMenu: [10, 25, 50, 100, 500, 1000],
                pageLength: 10,
                dom: 'lBfrtip'
            });

            // Event handler untuk tombol edit
            $('.btn-edit').click(function() {
                var id = $(this).data('id');
                var nama_supplier = $(this).data('nama_supplier');
                var alamat = $(this).data('alamat');
                var kontak = $(this).data('kontak');

                $('#id').val(id);
                $('#nama_supplier').val(nama_supplier);
                $('#alamat').val(alamat);
                $('#kontak').val(kontak);
                $('#action').val('edit');
                $('#submitBtn').text('Update Supplier');
            });

            // Event handler untuk tombol delete
            $('.btn-delete').click(function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Anda yakin?',
                    text: "Supplier akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('<form method="POST" action="supplier.php">')
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
                $('#nama_supplier').val('');
                $('#alamat').val('');
                $('#kontak').val('');
                $('#action').val('add');
                $('#submitBtn').text('Tambah Supplier');
            });
        });
    </script>
</body>
</html>
