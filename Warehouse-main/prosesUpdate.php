<?php

include 'koneksi.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $nama_barang = $_POST['nama_barang'];
    $jenis_barang = $_POST['jenis_barang'];
    $stock = $_POST['stock'];

    // Periksa apakah ada gambar baru yang diunggah
    if (isset($_FILES['gambar_barang'])) {
        $gambar_barang = $_FILES['gambar_barang']['name'];
        $gambar_barang_tmp = $_FILES['gambar_barang']['tmp_name'];
    }

    // Validasi formulir
    $errors = array();

    if (empty($nama_barang)) {
        $errors[] = 'Nama barang tidak boleh kosong.';
    }

    if (empty($jenis_barang)) {
        $errors[] = 'Jenis barang tidak boleh kosong.';
    }

    if (empty($stock)) {
        $errors[] = 'Stock tidak boleh kosong.';
    }

    if (isset($_FILES['gambar_barang']) && $_FILES['gambar_barang']['size'] > 0) {
        $maxFileSize = 2 * 1024 * 1024; // dalam bytes
        if ($_FILES['gambar_barang']['size'] > $maxFileSize) {
            $errors[] = "Ukuran file terlalu besar. Maksimum 2MB.";
        }
    }

    // Jika tidak ada kesalahan, lanjutkan dengan pembaruan
    if (empty($errors)) {
        // Hapus gambar lama jika ada
        $sql_select = "SELECT gambar_barang FROM barang WHERE id_barang = $id";
        $result_select = $koneksi->query($sql_select);
        if ($result_select->num_rows > 0) {
            $row_select = $result_select->fetch_assoc();
            $gambar_barang_lama = $row_select['gambar_barang'];
            if (!empty($gambar_barang_lama) && file_exists("upload/$gambar_barang_lama")) {
                unlink("upload/$gambar_barang_lama");
            }
        }

        // Jika ada gambar baru yang diunggah, pindahkan ke direktori "upload"
        if (isset($_FILES['gambar_barang']) && $_FILES['gambar_barang']['size'] > 0) {
            move_uploaded_file($gambar_barang_tmp, "upload/$gambar_barang");
        }

        // Perbarui data barang
        $sql_update = "UPDATE barang SET nama_barang = '$nama_barang', jenis_barang = '$jenis_barang', stock = '$stock'";

        // Tambahkan kondisi update gambar jika ada gambar baru yang diunggah
        if (isset($_FILES['gambar_barang']) && $_FILES['gambar_barang']['size'] > 0) {
            $sql_update .= ", gambar_barang = 'upload/$gambar_barang'";
        }

        $sql_update .= " WHERE id_barang = $id";

        if ($koneksi->query($sql_update) === TRUE) {
            $username = $_SESSION['username'];
            $staffQuery = "SELECT id_user FROM pengguna WHERE username = '$username'";
            $staffResult = $koneksi->query($staffQuery);
            $staffData = $staffResult->fetch_assoc();
            $staff_id = $staffData['id_user'];
            $tanggal_transaksi = date("Y-m-d");

            // Simpan informasi transaksi ke dalam tabel transaksi
            $historiQuery = "INSERT INTO transaksi (id_user, id_barang, jumlah, jenis_transaksi, tanggal_transaksi) VALUES ('$staff_id', '$id', '$stock', 'Mengupdate Barang $nama_barang ',  '$tanggal_transaksi')";
            if ($koneksi->query($historiQuery) === TRUE) {
                $response = "Anda telah mengupdate $nama_barang. Barang berhasil diubah.";
            } else {
                $response = "Error: " . $historiQuery . "<br>" . $koneksi->error;
            }
            header("Location: menuStaff.php");
            exit();
        } else {
            echo '<p>Terjadi kesalahan saat memperbarui data barang: ' . $koneksi->error . '</p>';
        }
    }
}

$koneksi->close();
