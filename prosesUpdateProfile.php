<?php

include 'koneksi.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include("koneksi.php");

if (isset($_POST['updateProfile'])) {
    $id_user = $_POST['id_user'];
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    
    // Hash password baru jika ada
    $password = $_POST['password']; // Password baru dari form
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE pengguna SET nama='$nama', username='$username', email='$email', password='$hashed_password' WHERE id_user=$id_user";
    } else {
        // Update data user tanpa mengubah password
        $sql = "UPDATE pengguna SET nama='$nama', username='$username', email='$email' WHERE id_user=$id_user";
    }

    if (mysqli_query($koneksi, $sql)) {
        header("Location: menuAdmin.php"); // Redirect ke halaman menuAdmin.php setelah berhasil update
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($koneksi);
    }
}
?>