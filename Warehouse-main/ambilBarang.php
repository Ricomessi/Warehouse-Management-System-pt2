<?php

include 'koneksi.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

if (isset($_GET['id'])) {
    $barang_id = $_GET['id'];

    // Dapatkan data barang berdasarkan ID
    $sql = "SELECT * FROM barang WHERE id_barang = $barang_id";
    $result = $koneksi->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nama_barang = $row['nama_barang'];
        $stock = $row['stock'];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $jumlah_ambil = $_POST['jumlah_ambil'];

            // Pastikan jumlah yang diambil tidak melebihi stok yang tersedia
            if ($jumlah_ambil <= $stock && $jumlah_ambil > 0) {
                $new_stock = $stock - $jumlah_ambil;

                // Update stok barang di database
                $update_sql = "UPDATE barang SET stock = $new_stock WHERE id_barang = $barang_id";
                if ($koneksi->query($update_sql) === TRUE) {
                    $barang_id = $row['id_barang'];

                    // Ambil ID staff dari session username
                    $username = $_SESSION['username'];
                    $staffQuery = "SELECT id_user FROM pengguna WHERE username = '$username'";
                    $staffResult = $koneksi->query($staffQuery);
                    $staffData = $staffResult->fetch_assoc();
                    $staff_id = $staffData['id_user'];
                    $tanggal_transaksi = date("Y-m-d");

                    // Simpan informasi transaksi ke dalam tabel transaksi
                    $historiQuery = "INSERT INTO transaksi (id_user, id_barang, jumlah, jenis_transaksi, tanggal_transaksi) VALUES ('$staff_id', '$barang_id', '$jumlah_ambil', 'Mengambil Barang $nama_barang sejumlah $jumlah_ambil',  '$tanggal_transaksi')";
                    if ($koneksi->query($historiQuery) === TRUE) {
                        $response = array(
                            'message' => "Anda telah mengambil $jumlah_ambil $nama_barang. Stok barang berhasil diubah.",
                            'success' => true
                        );
                    } else {
                        $response = array(
                            'message' => "Error: " . $historiQuery . "<br>" . $koneksi->error,
                            'success' => false
                        );
                    }
                } else {
                    $response = array(
                        'message' => "Error: " . $koneksi->error,
                        'success' => false
                    );
                }
            } else {
                $response = array(
                    'message' => "Jumlah yang diminta melebihi stok yang tersedia atau tidak valid.",
                    'success' => false
                );
            }

            echo json_encode($response); // Mengirim pesan sebagai respons JSON
            exit; // Keluar dari skrip PHP setelah menampilkan pesan respons
        }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambil Barang</title>
    <link rel="stylesheet" href="css/ambilBarang.css">
    
</head>

<body class="gradienHabibi">
    <div class="container">
        <div class="register">
            <h1>Detail Barang: <?php echo $nama_barang; ?></h1>
            <div class="line gradienHabibi"></div>

            
            <form id="ambilForm" method="post" action="" class="firstForm">
                <div class="details">
                <p>Stok Tersedia: <span id="stok"><?php echo $stock; ?></span></p>
                    <label for="jumlah_ambil"><p>Berapa jumlah barang yang anda ambil?</p></label>
                    <input class="registration" type="number" id="jumlah_ambil" name="jumlah_ambil" min="1" max="<?php echo $stock; ?>" required>
                    <div id="responseContainer"></div>
                </div>
                <div class="button-container">
                    <!-- Tombol Kembali -->
                    <a href="menuStaff.php" class="btn-back">Kembali</a>
                </div>
                <div class="button-container">
                    <button type="submit" name="tbSubmit">Ambil</button>
                </div>
            </form>

            <script>
                document.getElementById('ambilForm').addEventListener('submit', function(e) {
                    e.preventDefault(); // Prevent form submission

                    var jumlahAmbil = document.getElementById('jumlah_ambil').value;
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'ambilBarang.php?id=<?php echo $barang_id; ?>', true);
                    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState == XMLHttpRequest.DONE) {
                            if (xhr.status == 200) {
                                var response = JSON.parse(xhr.responseText);
                                document.getElementById('stok').textContent = <?php echo $stock; ?> - jumlahAmbil;
                                var responseContainer = document.getElementById('responseContainer');
                                responseContainer.innerHTML = response.message;

                                // Tambahkan warna berdasarkan kondisi
                                if (response.success) {
                                    responseContainer.style.color = '#28a745'; // Warna hijau
                                } else {
                                    responseContainer.style.color = '#dc3545'; // Warna merah
                                }
                            } else {
                                console.error('AJAX request error');
                            }
                        }
                    };
                    xhr.send('jumlah_ambil=' + jumlahAmbil);
                });
            </script>

            <?php
                } else {
                    echo "Barang tidak ditemukan.";
                }
            } else {
                echo "Invalid request.";
            }

            ?>
        </div>
    </div>
</body>

</html>
