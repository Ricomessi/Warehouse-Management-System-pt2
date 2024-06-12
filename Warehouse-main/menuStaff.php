<?php

include 'koneksi.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/menuStaff.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.bundle.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css">
    <link defer rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                    <li><a href="mainStaff.php"><span class="las la-adjust"></span><span>Dashboard</span></a></li>
                    <li><a href="#" class="active"><span class="ti ti-address-book"></span><span>Table Barang</span></a></li>
                    <!-- <li><a href="historiStaff.php"><span class="ti ti-history"></span><span>History Staff</span></a></li> -->
                </ul>
            </div>

            <div class="sidebar-card" style="position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%);">
                <a onclick="confirmLogout()" class="btn btn-main btn-block">
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
                    <p>Menampilkan hasil analisa Barang<span class="las la-chart-line"></span></p>
                </div>
            </div>
        </header>
        <main>

            <section>
                <div class="block-grid">
                    <div class="revenue-card">
                        <h3 class="section-head">Data Barang</h3>
                        <div class="rev-content">

                            <div class="row">
                                <div class="col-md-6">
                                    <form action="createBarang.php" method="post" class="mb-2 create-barang-form">
                                        <button type="submit" name="createBarang" class="btn btn-success">Tambah Data Barang</button>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form action="" method="get" class="mb-2 search-form">
                                        <div class="input-group">
                                            <input type="text" id="search-input" name="query" class="form-control" placeholder="Cari Nama">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-custom">Cari</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>



                            <div class="search-results mt-3">
                                <?php
                                $query = isset($_GET['query']) ? $_GET['query'] : '';
                                include 'koneksi.php';
                                $resultsPerPage = 5; // Number of results per page
                                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                                $offset = ($page - 1) * $resultsPerPage;

                                // Fetch data with pagination
                                if (!isset($_GET['query']) || empty($_GET['query'])) {
                                    $sql = "SELECT * FROM barang LIMIT $resultsPerPage OFFSET $offset"; // Fetch limited results if query is empty
                                } else {
                                    $query = $_GET['query'];
                                    $isNumeric = is_numeric($query);

                                    if ($isNumeric) {
                                        $sql = "SELECT * FROM barang WHERE id_barang = $query LIMIT $resultsPerPage OFFSET $offset";
                                    } else {
                                        $sql = "SELECT * FROM barang WHERE nama_barang LIKE '%$query%' LIMIT $resultsPerPage OFFSET $offset";
                                    }
                                }

                                $result = $koneksi->query($sql); // Run the SQL query

                                $searchResults = [];
                                $searchMessage = "";

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $searchResults[] = $row;
                                    }
                                } else {
                                    $searchMessage = "Tidak ada hasil pencarian untuk '$query'.";
                                }

                                // Fetch the total count of results
                                $totalCountQuery = "SELECT COUNT(*) AS total FROM barang";
                                if (!empty($query) && !$isNumeric) {
                                    $totalCountQuery = "SELECT COUNT(*) AS total FROM barang WHERE nama_barang LIKE '%$query%'";
                                } elseif (!empty($query) && $isNumeric) {
                                    $totalCountQuery = "SELECT COUNT(*) AS total FROM barang WHERE id_barang = $query";
                                }

                                $totalResult = $koneksi->query($totalCountQuery);
                                $total_records = $totalResult->fetch_assoc()['total'];

                                $total_pages = ceil($total_records / $resultsPerPage);

                                if (empty($searchResults)) {
                                    echo "<div class='search-results'><p>Data tidak ditemukan.</p></div>";
                                } else {
                                    echo "<table class='table'>";
                                    echo "<thead>";
                                    echo "<tr><th class='table-primary'>ID Barang</th>";
                                    echo "<th class='table-primary'>Nama Barang</th>";
                                    echo "<th class='table-primary'>Jenis Barang</th>";
                                    echo "<th class='table-primary'>Stock</th>";
                                    echo "<th class='table-primary'>Gambar Barang</th>";
                                    echo "<th class='table-primary'>Action</th>";
                                    echo "</tr></thead><tbody>";


                                    foreach ($searchResults as $result) {
                                        echo "<tr>";
                                        echo "<td>" . $result['id_barang'] . "</td>";
                                        echo "<td>" . $result['nama_barang'] . "</td>";
                                        echo "<td>" . $result['jenis_barang'] . "</td>";
                                        echo "<td>" . $result['stock'] . "</td>";
                                        echo "<td class='text-center'><img src='" . $result['gambar_barang'] . "' alt='Gambar Barang' style='width: 100px; height: 100px; object-fit: cover;' class='mx-auto'></td>";
                                        echo "<td>
        <div class='d-flex flex-column justify-content-center align-items-center'>
            <a href='ambilBarang.php?id=" . $result['id_barang'] . "' class='btn btn-sm btn-warning mb-2'>Ambil Barang</a>    
            <a href='updateBarang.php?id=" . $result['id_barang'] . "' class='btn btn-sm btn-primary mb-2'>Update</a>
            <button onclick='confirmDelete(" . $result['id_barang'] . ")' class='btn btn-sm btn-danger'>Delete</button>
        </div>
      </td>";

                                        echo "</tr>";
                                    }
                                    echo "</tbody></table></div>";

                                    if ($total_pages > 1) {
                                        echo '<div class="btn-group mt-3">';
                                        if ($page > 1) {
                                            $prev_page = $page - 1;
                                            echo '<a class="btn btn-custom" href="menuStaff.php?page=' . $prev_page . '">Previous</a>';
                                        }

                                        for ($i = 1; $i <= $total_pages; $i++) {
                                            echo '<a class="btn ' . ($i === $page ? 'btn-custom active' : 'btn-custom') . '" href="menuStaff.php?page=' . $i . '">' . $i . '</a>';
                                        }

                                        if ($page < $total_pages) {
                                            $next_page = $page + 1;
                                            echo '<a class="btn btn-custom" href="menuStaff.php?page=' . $next_page . '">Next</a>';
                                        }
                                        echo '</div>';
                                    }
                                }
                                ?>

                            </div>
                            <script>
                                $(document).ready(function() {
                                    $('#search-input').focus();

                                    $('#search-input').keyup(function() {
                                        var query = $(this).val(); // Ambil nilai input pencarian
                                        $.ajax({
                                            url: 'handleSearch.php', // Ganti dengan nama file PHP yang menangani pencarian
                                            method: 'GET',
                                            data: {
                                                query: query
                                            },
                                            success: function(response) {
                                                $('.search-results').html(
                                                    response
                                                ); // Perbarui bagian hasil pencarian dengan respons dari server
                                            }
                                        });
                                    });
                                });
                            </script>
                            <script>
                                // Konfirmasi delete dengan SweetAlert
                                function confirmDelete(id) {
                                    Swal.fire({
                                        title: 'Apakah Anda yakin?',
                                        text: 'Data akan dihapus permanen!',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#d33',
                                        cancelButtonColor: '#3085d6',
                                        confirmButtonText: 'Ya, hapus!',
                                        cancelButtonText: 'Batal'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            // Redirect atau lakukan tindakan delete di sini
                                            window.location.href = 'prosesDelete.php?id=' + id;
                                        }
                                    });
                                }
                            </script>
                            <script>
                                // Konfirmasi logout dengan SweetAlert
                                function confirmLogout() {
                                    Swal.fire({
                                        title: 'Apakah Anda yakin ingin keluar?',
                                        text: 'Anda akan logout dari akun ini.',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#d33',
                                        cancelButtonColor: '#3085d6',
                                        confirmButtonText: 'Ya, logout',
                                        cancelButtonText: 'Batal'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            // Redirect ke halaman logout di sini
                                            window.location.href = 'logout.php';
                                        }
                                    });
                                }
                            </script>

                        </div>
                    </div>
                </div>

            </section>
        </main>
    </div>
</body>



</html>