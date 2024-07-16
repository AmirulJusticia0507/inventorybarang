<?php
include 'konekke_local.php';

// Mulai sesi jika belum dimulai
session_start();

// Periksa apakah pengguna telah terautentikasi
if (!isset($_SESSION['userid'])) {
    // Jika tidak ada sesi pengguna, alihkan ke halaman login
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Purchase Order Details</title>
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
    <style>
        @media print {
            /* Hapus elemen yang tidak ingin dicetak, misalnya navigasi dan footer */
            nav, footer, .btn {
                display: none !important;
            }

            /* Atur tampilan tabel untuk cetak */
            table {
                width: 100%;
                border-collapse: collapse;
                border: 2px solid #000;
            }

            th, td {
                border: 1px solid #000;
                padding: 8px;
                text-align: left;
            }

            th {
                background-color: #f2f2f2;
            }
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
                            <div class="card-body">
                                <h2>Form Purchase Order Details</h2>
                                <form method="POST" action="purchase_order_details_process.php">
                                    <input type="hidden" id="action" name="action" value="add">
                                    <input type="hidden" id="id" name="id" value="">
                                    <div class="form-container">
                                        <div class="form-group">
                                            <label for="purchase_order_id">Purchase Order ID</label>
                                            <select class="form-control" id="purchase_order_id" name="purchase_order_id" required>
                                                <option value="">Pilih Purchase Order ID</option>
                                                <?php
                                                $sql_purchase_order = "SELECT purchase_order_id FROM purchase_orders";
                                                $result_purchase_order = $koneklocalhost->query($sql_purchase_order);
                                                while ($row_purchase_order = $result_purchase_order->fetch_assoc()) {
                                                    echo "<option value='" . $row_purchase_order['purchase_order_id'] . "'>" . $row_purchase_order['purchase_order_id'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="barang_id">Barang ID</label>
                                            <select class="form-control" id="barang_id" name="barang_id" required onchange="fetchBarangDetails(this.value)">
                                                <option value="">Pilih Barang ID</option>
                                                <?php
                                                $sql_barang = "SELECT id, nama_barang FROM barang";
                                                $result_barang = $koneklocalhost->query($sql_barang);
                                                while ($row_barang = $result_barang->fetch_assoc()) {
                                                    echo "<option value='" . $row_barang['id'] . "'>" . $row_barang['id'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nama_barang">Nama Barang</label>
                                                    <input type="text" class="form-control" id="nama_barang" name="nama_barang" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="harga_per_unit">Harga per Unit</label>
                                                    <input type="text" class="form-control" id="harga_per_unit" name="harga_per_unit" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="quantity">Quantity</label>
                                            <input type="number" class="form-control" id="quantity" name="quantity" required>
                                        </div>
                                    </div>
                                    <button type="submit" id="submitBtn" class="myButtonCekSaldo"><i class="fas fa-circle-info"></i> Tambah Detail</button>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card preview-card">
                                <div class="preview-container">
                                    <h3>Preview Detail Purchase Order</h3>
                                    <div id="detailPreview">
                                        <!-- Preview detail purchase order akan ditampilkan disini -->
                                    </div>
                                    <button class="btn btn-info mt-3" onclick="printPreview()"><i class="fas fa-print"></i> Print</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container-fluid mt-4">
                    <div class="card">
                        <div class="card-body">
                            <table id="purchaseorderDatatable" class="display table table-bordered table-striped table-hover responsive nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Purchase Order ID</th>
                                        <!-- <th>Barang ID</th> -->
                                        <th>Nama Barang</th>
                                        <th>Quantity</th>
                                        <th>Harga per Unit</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Ambil data purchase order details dari database
                                    $sql_details = "SELECT pod.id, pod.purchase_order_id, pod.barang_id, pod.quantity, pod.harga_per_unit, p.nama_barang 
                                    FROM purchase_order_details pod 
                                    JOIN barang p ON pod.barang_id = p.id";
                                    $result_details = $koneklocalhost->query($sql_details);
                                    $no = 1;
                                    while ($row = $result_details->fetch_assoc()) {
                                    ?>
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td><?php echo $row['purchase_order_id']; ?></td>
                                            <!-- <td><?php echo $row['barang_id']; ?></td> -->
                                            <td><?php echo $row['nama_barang']; ?></td>
                                            <td><?php echo $row['quantity']; ?></td>
                                            <td><?php echo $row['harga_per_unit']; ?></td>
                                            <td>
                                                <button class="btn btn-primary btn-sm" onclick="showDetail(<?php echo $row['id']; ?>)"><i class="fas fa-eye"></i> Detail</button>
                                                <button class="btn btn-warning btn-sm" onclick="editDetail(<?php echo $row['id']; ?>)"><i class="fas fa-edit"></i> Edit</button>
                                                <button class="btn btn-danger btn-sm" onclick="deleteDetail(<?php echo $row['id']; ?>)"><i class="fas fa-trash-alt"></i> Hapus</button>
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

                    <div class="modal fade" id="detailPreviewModal" tabindex="-1" aria-labelledby="detailPreviewModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="detailPreviewModalLabel">Preview Detail Purchase Order</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="detailPreview">
                                        <!-- Preview detail purchase order akan ditampilkan disini -->
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Div untuk menyimpan konten yang akan dicetak -->
                    <div id="printableContent" style="display: none;">
                        <!-- Di sini akan ditempatkan tabel preview saat fungsi printPreview dijalankan -->
                    </div>
                </div>
            </main>
        </div>
        <?php include 'footer.php'; ?>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.11.15/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#purchaseorderDatatable').DataTable({
                responsive: true,
                scrollX: true,
                searching: true,
                lengthMenu: [10, 25, 50, 100, 500, 1000],
                pageLength: 10,
                dom: 'lBfrtip'
            });
        });

        // Fungsi untuk mengambil data detail purchase order untuk diedit
        function editDetail(id) {
            $.ajax({
                type: 'POST',
                url: 'get_purchase_order_detail.php',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    $('#action').val('edit'); // Mengatur nilai action menjadi 'edit'
                    $('#id').val(response.id); // Mengisi nilai id
                    $('#purchase_order_id').val(response.purchase_order_id); // Mengisi nilai purchase_order_id
                    $('#barang_id').val(response.barang_id); // Mengisi nilai barang_id
                    $('#quantity').val(response.quantity); // Mengisi nilai quantity
                    $('#harga_per_unit').val(response.harga_per_unit); // Mengisi nilai harga_per_unit

                    $('#submitBtn').html('<i class="fas fa-edit"></i> Update Detail'); // Mengubah teks tombol submit
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan saat mengambil data'
                    });
                }
            });
        }

        // Fungsi untuk menambahkan atau mengedit detail purchase order
        function submitForm() {
                var action = $('#action').val();
                var id = $('#id').val();
                var purchase_order_id = $('#purchase_order_id').val();
                var barang_id = $('#barang_id').val();
                var quantity = $('#quantity').val();
                var harga_per_unit = $('#harga_per_unit').val();

                $.ajax({
                    type: 'POST',
                    url: 'purchase_order_details_process.php',
                    data: {
                        action: action,
                        id: id,
                        purchase_order_id: purchase_order_id,
                        barang_id: barang_id,
                        quantity: quantity,
                        harga_per_unit: harga_per_unit
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload(); // Refresh halaman setelah berhasil update
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Terjadi kesalahan saat mengirim data'
                        });
                    }
                });
            }

            // Fungsi untuk menghapus detail purchase order
            function deleteDetail(id) {
                Swal.fire({
                    title: 'Anda yakin?',
                    text: "Anda tidak akan dapat mengembalikan ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: 'purchase_order_details_process.php',
                            data: {
                                action: 'delete',
                                id: id
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: response.message
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            location.reload(); // Refresh halaman setelah berhasil delete
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: response.message
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Terjadi kesalahan saat menghapus data'
                                });
                            }
                        });
                    }
                });
            }

        // Fungsi untuk mencetak preview detail purchase order
        function printPreview() {
            // Buat tabel untuk preview detail
            var previewTable = `
                <table>
                    <thead>
                        <tr>
                            <th>Detail Purchase Order</th>
                            <th>Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Purchase Order ID</td>
                            <td>${selectedDetail.purchase_order_id}</td>
                        </tr>
                        <tr>
                            <td>Barang ID</td>
                            <td>${selectedDetail.barang_id}</td>
                        </tr>
                        <tr>
                            <td>Quantity</td>
                            <td>${selectedDetail.quantity}</td>
                        </tr>
                        <tr>
                            <td>Harga per Unit</td>
                            <td>${selectedDetail.harga_per_unit}</td>
                        </tr>
                        <tr>
                            <td>Gambar Produk</td>
                            <td id="gambarProdukTd">
                                ${selectedDetail.photo_product && selectedDetail.photo_product !== 'null' ?
                                    `<img src="uploads/products/${selectedDetail.photo_product}" alt="${selectedDetail.nama_barang}" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">` :
                                    `<span class="text-danger">Gambar tidak ditemukan</span>`
                                }
                            </td>
                        </tr>
                    </tbody>
                </table>
            `;

            // Tempatkan tabel preview dalam div yang akan dicetak
            $('#printableContent').html(previewTable);

            // Cetak halaman
            window.print();
        }

        // Variabel global untuk menyimpan data detail purchase order
        var selectedDetail = null;

        // Fungsi untuk menampilkan detail purchase order menggunakan Ajax
        function showDetail(id) {
            $.ajax({
                type: 'POST',
                url: 'get_purchase_order_detail.php',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    selectedDetail = response; // Simpan response ke variabel global

                    // Buat HTML untuk gambar produk
                    var imageHtml = response.photo_product && response.photo_product !== 'null' ?
                        `<img src="uploads/products/${response.photo_product}" alt="${response.nama_barang}" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">` :
                        `<span class="text-danger">Gambar tidak ditemukan</span>`;

                    var previewTable = `
                        <table>
                            <thead>
                                <tr>
                                    <th colspan="2">Detail Purchase Order</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Purchase Order ID</td>
                                    <td>${response.purchase_order_id}</td>
                                </tr>
                                <tr>
                                    <td>Barang ID</td>
                                    <td>${response.barang_id}</td>
                                </tr>
                                <tr>
                                    <td>Quantity</td>
                                    <td>${response.quantity}</td>
                                </tr>
                                <tr>
                                    <td>Harga per Unit</td>
                                    <td>${response.harga_per_unit}</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-center">
                                        <div class="d-flex flex-column align-items-center">
                                            <span>${response.nama_barang}</span>
                                            <div id="gambarProduk" class="mt-2">
                                                ${imageHtml}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    `;

                    $('#detailPreview').html(previewTable); // Tampilkan preview di dalam elemen dengan id 'detailPreview'
                    $('#detailPreviewModal').modal('show'); // Tampilkan modal preview
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan saat mengambil data'
                    });
                }
            });
        }


        function fetchBarangDetails(barang_id) {
            $.ajax({
                url: 'fetch_barang_details.php',
                type: 'POST',
                data: {barang_id: barang_id},
                dataType: 'json',
                success: function(response) {
                    $('#nama_barang').val(response.nama_barang);
                    $('#harga_per_unit').val(response.harga_per_unit);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
        function showSuccessMessage(message) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                showConfirmButton: false,
                timer: 1500
            });
        }
    </script>
</body>
</html>
