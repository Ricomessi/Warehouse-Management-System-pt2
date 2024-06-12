<?php
require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;

$factory = (new Factory())
    ->withServiceAccount('warehouse-93629-firebase-adminsdk-nh8xv-2f532020c3.json')
    ->withDatabaseUri('https://warehouse-93629-default-rtdb.firebaseio.com/');

$database = $factory->createDatabase();

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch data of the barang before deleting it
    $barangRef = $database->getReference('barang/' . $id);
    $barangSnapshot = $barangRef->getSnapshot();
    $barangData = $barangSnapshot->getValue();

    if ($barangData) {
        $nama_barang = $barangData['nama_barang'];
        $stock = $barangData['stock'];
        // $username = $_SESSION['username'];

        // // Fetch staff user ID
        // $penggunaRef = $database->getReference('pengguna')->orderByChild('username')->equalTo($username)->getSnapshot();
        // $staffData = null;
        // foreach ($penggunaRef->getValue() as $key => $value) {
        //     $staffData = $value;
        //     break;
        // }
        // if ($staffData) {
        //     $staff_id = $staffData['id_user'];
        //     $tanggal_transaksi = date("Y-m-d");

        // Insert into transaksi (history)
        // $newTransaksi = [
        //     'id_user' => $staff_id,
        //     'id_barang' => $id,
        //     'jumlah' => $stock,
        //     'jenis_transaksi' => 'Menghapus Barang ' . $nama_barang,
        //     'tanggal_transaksi' => $tanggal_transaksi
        // ];
        // $database->getReference('transaksi')->push($newTransaksi);

        // // Delete all related transaksi of the barang
        // $transaksiRef = $database->getReference('transaksi')->orderByChild('id_barang')->equalTo($id)->getSnapshot();
        // foreach ($transaksiRef->getValue() as $key => $transaksi) {
        //     $database->getReference('transaksi/' . $key)->remove();
        // }

        // Delete the barang
        $barangRef->remove();

        // Set session success and redirect
        $success = "Data Barang Berhasil Diperbarui!!";
        $_SESSION['success'] = $success;
        header("Location: menuStaff.php");
    } else {
        echo "Error: Failed to fetch staff data.";
    }
    // } else {
    //     echo "Error: Failed to fetch barang data.";
    // }
}
