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
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Barang</title>
    <link rel="stylesheet" href="css/updateBarang.css">

</head>


<body class="gradienHabibi">
    <div class="container">
        <div class="register">
            <h1>Update Barang</h1>
            <div class="line gradienHabibi"></div>


            <?php
            include 'koneksi.php';

            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $sql = "SELECT * FROM barang WHERE id_barang = $id";
                $result = $koneksi->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
            ?>

                    <form action="prosesUpdate.php" method="post" enctype="multipart/form-data" class="firstForm">
                        <input type="hidden" name="id" value="<?php echo $row['id_barang']; ?>">




                        <div class="details">
                            <label for="nama_barang">
                                <p>Nama Barang</p>
                            </label>
                            <input class="registration" type="text" name="nama_barang" value="<?php echo $row['nama_barang']; ?>" required>
                        </div>

                        <div class="details">
                            <label for="jenis_barang">
                                <p>Jenis Barang</p>
                            </label>
                            <select class="registration" name="jenis_barang">
                                <option value="Elektronik" <?php if ($row['jenis_barang'] == 'Elektronik') echo 'selected'; ?>>
                                    Elektronik</option>
                                <option value="Pakaian" <?php if ($row['jenis_barang'] == 'Pakaian') echo 'selected'; ?>>
                                    Pakaian</option>
                                <option value="Makanan" <?php if ($row['jenis_barang'] == 'Makanan') echo 'selected'; ?>>
                                    Makanan</option>
                                <option value="Minuman" <?php if ($row['jenis_barang'] == 'Minuman') echo 'selected'; ?>>
                                    Minuman</option>
                                <option value="Alat Tulis" <?php if ($row['jenis_barang'] == 'Alat Tulis') echo 'selected'; ?>>
                                    Alat Tulis</option>
                                <option value="Mainan" <?php if ($row['jenis_barang'] == 'Mainan') echo 'selected'; ?>>
                                    Mainan</option>
                                <option value="Otomotif" <?php if ($row['jenis_barang'] == 'Otomotif') echo 'selected'; ?>>
                                    Otomotif</option>
                                <option value="Perabotan" <?php if ($row['jenis_barang'] == 'Perabotan') echo 'selected'; ?>>
                                    Perabotan</option>
                                <option value="Barang Antik" <?php if ($row['jenis_barang'] == 'Barang Antik') echo 'selected'; ?>>
                                    Barang Antik</option>
                            </select>
                        </div>

                        <div class="details">
                            <label for="stock">
                                <p>Stock</p>
                            </label>
                            <input class="registration" type="number" name="stock" value="<?php echo $row['stock']; ?>" required>

                        </div>

                        <div class="details">
                            <label for="gambar_barang">
                                <p>Gambar Barang</p>
                            </label>
                            <div class="gambar-container">
                                <?php
                                $gambarPath =  $row['gambar_barang'];
                                if (file_exists($gambarPath)) {
                                    echo '<img src="' . $gambarPath . '" alt="Gambar Barang" class="gambar-preview bulat">';
                                } else {
                                    echo 'Gambar tidak ditemukan';
                                }
                                ?>
                            </div>
                            <input class="registration" type="file" name="gambar_barang">
                        </div>


                        <div class="button-container">
                            <!-- Tombol Kembali -->
                            <a href="menuStaff.php" class="btn-back">Kembali</a>
                        </div>
                        <div class="button-container">
                            <!-- Tombol Register -->
                            <button type="submit" name="tbSubmit">Update</button>
                        </div>
                    </form>

            <?php
                } else {
                    echo '<p>Data barang tidak ditemukan.</p>';
                }
            } else {
                echo '<p>ID barang tidak ditemukan.</p>';
            }
            ?>

            <?php


            if (!empty($_SESSION['errors'])) :
            ?>
                <div class="text-danger">
                    <h4>Pesan Kesalahan Memperbarui Data Barang:</h4>
                    <ul>
                        <?php foreach ($_SESSION['errors'] as $error) : ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php elseif (!empty($_SESSION['success'])) : ?>
                <div class="text-success">
                    <p><?php echo $_SESSION['success']; ?></p>
                </div>
            <?php endif; ?>

            <?php
            // Jangan lupa unset sesuai kebutuhan
            unset($_SESSION['errors']);
            unset($_SESSION['success']);
            ?>






        </div>
    </div>
</body>

</html>