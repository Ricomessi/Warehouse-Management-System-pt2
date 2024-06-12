<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_barang = $_POST['nama_barang'];
    $jenis_barang = $_POST['jenis_barang'];
    $stock = $_POST['stock'];
    $gambar_barang = $_FILES['gambar_barang'];

    $errors = array();

    if (empty($nama_barang)) {
        $errors[] = "Nama Barang tidak boleh kosong";
    } elseif (!preg_match("/^[a-zA-Z0-9 ]*$/", $nama_barang)) {
        $errors[] = "Nama Barang hanya boleh berisi huruf, angka, dan spasi";
    }

    if (empty($jenis_barang)) {
        $errors[] = "Pilih jenis barang";
    }

    if (empty($stock) || !is_numeric($stock) || $stock < 0) {
        $errors[] = "Stock tidak valid";
    }

    // Check if a file has been uploaded
    if (!empty($gambar_barang['name'])) {
        $allowedFormats = array("jpg", "jpeg", "png", "gif");
        $imageFileType = strtolower(pathinfo($gambar_barang['name'], PATHINFO_EXTENSION));
        $isValidImage = getimagesize($gambar_barang['tmp_name']);

        if (!in_array($imageFileType, $allowedFormats)) {
            $errors[] = "Hanya file JPG, JPEG, PNG, dan GIF yang diizinkan.";
        }

        if (!$isValidImage) {
            $errors[] = "File yang diunggah bukan gambar.";
        }

        $maxFileSize = 2 * 1024 * 1024;
        if ($gambar_barang['size'] > $maxFileSize) {
            $errors[] = "Ukuran file terlalu besar. Maksimum 2MB.";
        }
    } else {
        $errors[] = "Gambar Barang tidak boleh kosong";
    }



    if (empty($errors)) {
        $uploadDir = 'upload/';
        $profilePictureFileName = basename($gambar_barang['name']);
        $uploadPath = $uploadDir . $profilePictureFileName;

        // Periksa apakah barang sudah ada dalam database
        $checkIfExistsQuery = "SELECT * FROM barang WHERE nama_barang = '$nama_barang'";
        $result = $koneksi->query($checkIfExistsQuery);

        if ($result->num_rows > 0) {
            // Jika barang sudah ada, tambahkan stok baru
            $row = $result->fetch_assoc();
            $existingStock = $row['stock'];
            $newStock = $existingStock + $stock;

            $updateQuery = "UPDATE barang SET stock = '$newStock' WHERE nama_barang = '$nama_barang'";
            if ($koneksi->query($updateQuery) === TRUE) {
                // Ambil ID barang yang sudah ada
                $barang_id = $row['id_barang'];

                // Ambil ID staff dari session username
                $username = $_SESSION['username'];
                $staffQuery = "SELECT id_user FROM pengguna WHERE username = '$username'";
                $staffResult = $koneksi->query($staffQuery);
                $staffData = $staffResult->fetch_assoc();
                $staff_id = $staffData['id_user'];
                $tanggal_transaksi = date("Y-m-d");

                // Simpan informasi transaksi ke dalam tabel transaksi
                $historiQuery = "INSERT INTO transaksi (id_user, id_barang, jumlah, jenis_transaksi, tanggal_transaksi) VALUES ('$staff_id', '$barang_id', '$stock', 'Menambah Stok Barang $nama_barang',  '$tanggal_transaksi')";
                if ($koneksi->query($historiQuery) === TRUE) {
                    // Set session success
                    $success = "Data Barang Berhasil Diperbarui!!";
                    $_SESSION['success'] = $success;
                    header("Location: menuStaff.php");
                } else {
                    echo "Error: " . $historiQuery . "<br>" . $koneksi->error;
                }
            } else {
                echo "Error: " . $updateQuery . "<br>" . $koneksi->error;
            }
        } else {
            // Jika barang belum ada, tambahkan barang baru
            if (move_uploaded_file($gambar_barang['tmp_name'], $uploadPath)) {
                $sql = "INSERT INTO barang (nama_barang, jenis_barang, stock, gambar_barang) VALUES ('$nama_barang', '$jenis_barang', '$stock', '$uploadPath')";

                // Setelah proses insert atau update selesai
                if ($koneksi->query($sql) === TRUE) {
                    // Jika proses insert berhasil, simpan histori transaksi
                    $barang_id = $koneksi->insert_id; // Ambil ID barang yang baru saja di-insert

                    // Ambil ID staff dari session username
                    $username = $_SESSION['username'];
                    $staffQuery = "SELECT id_user FROM pengguna WHERE username = '$username'";
                    $staffResult = $koneksi->query($staffQuery);
                    $staffData = $staffResult->fetch_assoc();
                    $staff_id = $staffData['id_user'];
                    $tanggal_transaksi = date("Y-m-d");

                    // Simpan informasi transaksi ke dalam tabel histori_staff
                    $historiQuery = "INSERT INTO transaksi (id_user, id_barang, jumlah, jenis_transaksi, tanggal_transaksi) VALUES ('$staff_id', '$barang_id', '$stock', 'Menambah Barang $nama_barang',  '$tanggal_transaksi')";
                    if ($koneksi->query($historiQuery) === TRUE) {
                        // Set session success
                        $success = "Data Barang Baru Berhasil Ditambahkan!!";
                        $_SESSION['success'] = $success;
                        header("Location: menuStaff.php");
                    } else {
                        echo "Error: " . $historiQuery . "<br>" . $koneksi->error;
                    }
                } else {
                    // Jika proses insert/update gagal, tampilkan pesan error
                    echo "Error: " . $sql . "<br>" . $koneksi->error;
                }
            } else {
                $errors[] = "Gagal mengunggah file. Silakan coba lagi.";
                $_SESSION['errors'] = $errors;
                header("Location: createBarang.php");
            }
        }
    } else {
        $_SESSION['errors'] = $errors;
        header("Location: createBarang.php");
    }
}
