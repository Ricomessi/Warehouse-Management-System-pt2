<?php

include 'firebaseconfig.php';
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
        // Dapatkan data barang lama
        $reference = $database->getReference('barang/' . $id);
        $snapshot = $reference->getSnapshot();
        $barang_lama = $snapshot->getValue();

        if ($barang_lama) {
            $gambar_barang_lama = $barang_lama['gambar_barang'];
            if (!empty($gambar_barang_lama) && file_exists($gambar_barang_lama)) {
                unlink($gambar_barang_lama);
            }
        }

        // Jika ada gambar baru yang diunggah, pindahkan ke direktori "upload"
        if (isset($_FILES['gambar_barang']) && $_FILES['gambar_barang']['size'] > 0) {
            $uploadPath = 'upload/' . $gambar_barang;
            move_uploaded_file($gambar_barang_tmp, $uploadPath);
        } else {
            $uploadPath = $barang_lama['gambar_barang'];
        }

        // Perbarui data barang
        $updatedBarang = [
            'nama_barang' => $nama_barang,
            'jenis_barang' => $jenis_barang,
            'stock' => $stock,
            'gambar_barang' => $uploadPath
        ];

        $reference->update($updatedBarang);
        $response = "Anda telah mengupdate $nama_barang. Barang berhasil diubah.";
        header("Location: menuStaff.php");

        $username = $_SESSION['username'];
        $staffReference = $database->getReference('pengguna')->orderByChild('username')->equalTo($username)->getSnapshot();
        if ($staffReference->numChildren() > 0) {
            $staffData = array_values($staffReference->getValue())[0];
            $staff_id = $staffData['id_user'];
            $tanggal_transaksi = date("Y-m-d");

            // Simpan informasi transaksi ke dalam tabel transaksi
            $transaksiData = [
                'id_user' => $staff_id,
                'id_barang' => $id,
                'jumlah' => $stock,
                'jenis_transaksi' => "Mengupdate Barang $nama_barang",
                'tanggal_transaksi' => $tanggal_transaksi
            ];

            $database->getReference('transaksi')->push($transaksiData);

            $response = "Anda telah mengupdate $nama_barang. Barang berhasil diubah.";
            header("Location: menuStaff.php");
            exit();
        } else {
            $response = "Error: Pengguna tidak ditemukan.";
        }
    } else {
        $_SESSION['errors'] = $errors;
        header("Location: updateBarang.php?id=" . $id);
        exit();
    }
}
