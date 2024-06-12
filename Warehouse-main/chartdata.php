<?php
include 'koneksi.php';

// Fetch data from the database
$query = "SELECT Jenis_barang, COUNT(*) as count FROM barang GROUP BY Jenis_barang";
$result = $koneksi->query($query);

$data = [
    'labels' => [],
    'values' => [],
];

while ($row = $result->fetch_assoc()) {
    $data['labels'][] = $row['Jenis_barang'];
    $data['values'][] = $row['count'];
}

header('Content-Type: application/json');
echo json_encode($data);

$koneksi->close();
