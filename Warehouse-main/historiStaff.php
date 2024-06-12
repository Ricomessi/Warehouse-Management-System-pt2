<?php

include 'koneksi.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include("koneksi.php");
$query = "SELECT * FROM transaksi";
$result = mysqli_query($koneksi, $query);

// Pastikan koneksi dan query berhasil
if (!$result) {
    die("Gagal mengambil data: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/historyStaff.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.bundle.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css">
    <link defer rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>

<body class="gradienHabibi">
    <input type="checkbox" name="" id="menu-toggle">
    <div class="overlay"><label for="menu-toggle"></label></div>

    <div class="sidebar">
        <div class="sidebar-container" style="position: relative; margin-bottom: 120px;">
            <div class="brand">
                <img class="brand-img" src="img/logo.png" alt="logo" style="width:200px; margin-bottom:2rem">
            </div>

            <div class="sidebar-menu">
                <ul>
                    <li><a href="mainAdmin.php"><span class="las la-adjust"></span><span>Dashboard</span></a></li>
                    <li><a href="menuAdmin.php"><span class="ti ti-address-book"></span><span>Table Staff</span></a></li>
                    <li><a href="#" class="active"><span class="ti ti-history"></span><span>History Staff</span></a></li>
                </ul>
            </div>

            <div class="sidebar-card" style="position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%);">
                <a href="logout.php" class="btn btn-main btn-block">
                    <i class="ti ti-logout-2"></i> Log Out
                </a>
            </div>
        </div>
    </div>

    <div class="main-content">
        <header>
            <div class="header-wrapper">
                <label for="menu-toggle">
                    <span class="las la-bars"></span>
                </label>
                <div class="header-title">
                    <h1>Analisa</h1>
                    <p>Menampilkan hasil analisa transaksi<span class="las la-chart-line"></span></p>
                </div>
            </div>
        </header>
        <main>
            
        <section>
                <div class="block-grid">
                    <div class="revenue-card">
                        <h3 class="section-head">History Staff</h3>
                        <div class="rev-content">
                            <?php
                            $sql_total = "SELECT COUNT(*) as total_records FROM transaksi";
                            $result_total = $koneksi->query($sql_total);
                            $row_total = $result_total->fetch_assoc();
                            $total_records = $row_total['total_records'];

                            $records_per_page = 5;

                            // Halaman yang ditampilkan, diambil dari parameter "page" dalam URL
                            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

                            // Hitung offset (posisi awal data untuk halaman saat ini)
                            $offset = ($page - 1) * $records_per_page;

                            // Query untuk mengambil data dari tabel 'pengguna' berdasarkan paginasi
                            $sql = "SELECT id_transaksi, id_user, id_barang, jumlah, tanggal_transaksi, jenis_transaksi FROM transaksi LIMIT $records_per_page OFFSET $offset";
                            $result = $koneksi->query($sql);
                            ?>

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="table-primary">ID User</th>
                                        <th class="table-primary">ID Barang</th>
                                        <th class="table-primary">Jumlah</th>
                                        <th class="table-primary">Tanggal Transaksi</th>
                                        <th class="table-primary">Jenis Transaksi</th>
                                        <th class="table-primary">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                        <tr>
                                            <td><?php echo $row['id_user']; ?></td>
                                            <td><?php echo $row['id_barang']; ?></td>
                                            <td><?php echo $row['jumlah']; ?></td>
                                            <td><?php echo $row['tanggal_transaksi']; ?></td>
                                            <td><?php echo $row['jenis_transaksi']; ?></td>

                                            <td>
                                                <div class="d-flex flex-column justify-content-center align-items-center">
                                                    <a href="printHistori.php?id=<?php echo $row['id_transaksi']; ?>" class="btn btn-sm btn-primary mb-2">Print History</a>
                                                    
                                                </div>
                                            </td>

                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                            <?php
                            $total_pages = ceil($total_records / $records_per_page);

                            if ($total_pages > 1) {
                                echo '<div class="btn-group mt-3">';
                                if ($page > 1) {
                                    $prev_page = $page - 1;
                                    echo '<a class="btn btn-primary" href="historiStaff.php?page=' . $prev_page . '">Previous</a>';
                                }
                            
                                for ($i = 1; $i <= $total_pages; $i++) {
                                    echo '<a class="btn btn-primary ' . ($i === $page ? 'active' : '') . '" href="historiStaff.php?page=' . $i . '">' . $i . '</a>';
                                }
                            
                                if ($page < $total_pages) {
                                    $next_page = $page + 1;
                                    echo '<a class="btn btn-primary" href="historiStaff.php?page=' . $next_page . '">Next</a>';
                                }
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
            </section>
        </main>
    </div>
</body>

</html>
