<?php
include 'koneksi.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch data of the barang before deleting it
    $barangQuery = "SELECT nama_barang, stock FROM barang WHERE id_barang = $id";
    $barangResult = $koneksi->query($barangQuery);

    if ($barangResult) {
        $row = $barangResult->fetch_assoc();
        $nama_barang = $row['nama_barang'];
        $stock = $row['stock'];
        $username = $_SESSION['username'];
        $staffQuery = "SELECT id_user FROM pengguna WHERE username = '$username'";
        $staffResult = $koneksi->query($staffQuery);
        $staffData = $staffResult->fetch_assoc();
        $staff_id = $staffData['id_user'];
        $tanggal_transaksi = date("Y-m-d");

        $historiQuery = "INSERT INTO transaksi (id_user, id_barang, jumlah, jenis_transaksi, tanggal_transaksi) VALUES ('$staff_id', '$id', '$stock', 'Menghapus Barang $nama_barang',  '$tanggal_transaksi')";
        if ($koneksi->query($historiQuery) === TRUE) {
            // Set session success and redirect
            $success = "Data Barang Berhasil Diperbarui!!";
            $_SESSION['success'] = $success;
            $deleteTransactionsQuery = "DELETE FROM transaksi WHERE id_barang = $id";
            if ($koneksi->query($deleteTransactionsQuery) === TRUE) {
                // Then delete the barang after related transactions are deleted
                $deleteBarangQuery = "DELETE FROM barang WHERE id_barang = $id";
                if ($koneksi->query($deleteBarangQuery) === TRUE) {
                    // Proceed with adding transaction history after successful deletion

                    header("Location: menuStaff.php");
                } else {
                    echo "Error: " . $historiQuery . "<br>" . $koneksi->error;
                }
            } else {
                echo "Error: " . $deleteBarangQuery . "<br>" . $koneksi->error;
            }
        } else {
            echo "Error: " . $deleteTransactionsQuery . "<br>" . $koneksi->error;
        }
    } else {
        echo "Error: Failed to fetch barang data.<br>" . $koneksi->error;
    }
}

$koneksi->close();
