<?php
require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;

// Konfigurasi Firebase
$factory = (new Factory())
    ->withServiceAccount(__DIR__ . '/warehouse-93629-firebase-adminsdk-nh8xv-2f532020c3.json')
    ->withDatabaseUri('https://warehouse-93629-default-rtdb.firebaseio.com/');

$database = $factory->createDatabase();

// Ambil data dari Firebase Realtime Database
$reference = $database->getReference('barang');
$snapshot = $reference->getSnapshot();

$barangData = $snapshot->getValue();

$jenisBarangCount = [];

if (is_array($barangData)) {
    foreach ($barangData as $barang) {
        $jenis = $barang['Jenis_barang'] ?? 'Unknown';
        if (isset($jenisBarangCount[$jenis])) {
            $jenisBarangCount[$jenis]++;
        } else {
            $jenisBarangCount[$jenis] = 1;
        }
    }
}

// Format data untuk Chart.js
$data = [
    'labels' => array_keys($jenisBarangCount),
    'values' => array_values($jenisBarangCount),
];

// Menghasilkan JSON yang valid
header('Content-Type: application/json');
echo json_encode($data);
