<?php
$query = isset($_GET['query']) ? $_GET['query'] : '';
include 'koneksi.php';

$resultsPerPage = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $resultsPerPage;

$sql = "SELECT * FROM barang WHERE nama_barang LIKE ? OR id_barang LIKE ? LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($koneksi, $sql);

if ($stmt) {
    $searchQuery = "%$query%";
    mysqli_stmt_bind_param($stmt, "ssii", $searchQuery, $searchQuery, $resultsPerPage, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            echo "<table class='table'>";
            echo "<thead>";
            echo "<tr><th class='table-primary'>ID Barang</th>";
            echo "<th class='table-primary'>Nama Barang</th>";
            echo "<th class='table-primary'>Jenis Barang</th>";
            echo "<th class='table-primary'>Stock</th>";
            echo "<th class='table-primary'>Gambar Barang</th>";
            echo "<th class='table-primary'>Action</th>";
            echo "</tr></thead><tbody>";

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['id_barang'] . "</td>";
                echo "<td>" . $row['nama_barang'] . "</td>";
                echo "<td>" . $row['jenis_barang'] . "</td>";
                echo "<td>" . $row['stock'] . "</td>";
                echo "<td class='text-center'><img src='" . $row['gambar_barang'] . "' alt='Gambar Barang' style='width: 100px; height: 100px; object-fit: cover;' class='mx-auto'></td>";
                echo "<td>
                        <div class='d-flex flex-column justify-content-center align-items-center'>
                            <a href='ambilBarang.php?id=" . $row['id_barang'] . "' class='btn btn-sm btn-warning mb-2'>Ambil Barang</a>    
                            <a href='updateBarang.php?id=" . $row['id_barang'] . "' class='btn btn-sm btn-primary mb-2'>Update</a>
                            <a href='deleteBarang.php?id=" . $row['id_barang'] . "' class='btn btn-sm btn-danger'>Delete</a>     
                        </div>
                      </td>";
                echo "</tr>";
            }
            

            echo "</tbody></table></div>";
        } else {
            echo "<div class='search-results'><p>Data tidak ditemukan.</p></div>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<div class='search-results'><p>Terjadi kesalahan dalam menjalankan query.</p></div>";
    }
} else {
    echo "<div class='search-results'><p>Terjadi kesalahan dalam membuat statement.</p></div>";
}

mysqli_close($koneksi);
?>