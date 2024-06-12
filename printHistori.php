<?php
require_once('TCPDF-main/tcpdf.php');
include("koneksi.php");

if (isset($_GET['id'])) {
    $id_transaksi = $_GET['id'];

    // Query untuk mendapatkan informasi transaksi berdasarkan id_transaksi
    $sql = "SELECT pengguna.nama AS nama_user, pengguna.role, barang.nama_barang, barang.jenis_barang, transaksi.jumlah, transaksi.tanggal_transaksi, transaksi.jenis_transaksi 
            FROM transaksi 
            INNER JOIN pengguna ON transaksi.id_user = pengguna.id_user 
            INNER JOIN barang ON transaksi.id_barang = barang.id_barang 
            WHERE transaksi.id_transaksi = $id_transaksi";

    $result = $koneksi->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Buat instance TCPDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set dokumen meta-data
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Histori Transaksi');
        $pdf->SetSubject('Histori Transaksi');
        $pdf->SetKeywords('Histori, Transaksi, PDF');

        // Set margin dokumen
        $pdf->SetMargins(10, 10, 10);

        // Tambahkan halaman baru
        $pdf->AddPage();

        // Tambahkan konten ke PDF
        $html = '<h1 style="text-align: center; font-size: 18px;">Histori Transaksi</h1>';
        $html .= '<hr>'; // Garis pemisah
        $html .= '<table style="width: 100%;">';
        $html .= '<tr><td style="width: 40%;"><strong>Nama User:</strong></td><td>' . $row['nama_user'] . '</td></tr>';
        $html .= '<tr><td><strong>Role:</strong></td><td>' . $row['role'] . '</td></tr>';
        $html .= '<tr><td><strong>Nama Barang:</strong></td><td>' . $row['nama_barang'] . '</td></tr>';
        $html .= '<tr><td><strong>Jenis Barang:</strong></td><td>' . $row['jenis_barang'] . '</td></tr>';
        $html .= '<tr><td><strong>Jumlah:</strong></td><td>' . $row['jumlah'] . '</td></tr>';
        $html .= '<tr><td><strong>Tanggal Transaksi:</strong></td><td>' . $row['tanggal_transaksi'] . '</td></tr>';
        $html .= '<tr><td><strong>Jenis Transaksi:</strong></td><td>' . $row['jenis_transaksi'] . '</td></tr>';
        $html .= '</table>';
        $html .= '<hr>'; // Garis pemisah

        // Output HTML ke file PDF
        $pdf->SetFont('helvetica', '', 12);
        $pdf->writeHTML($html, true, false, true, false, '');

        // Menyimpan file PDF
        $pdf->Output('histori_transaksi.pdf', 'D');
    } else {
        echo "Data tidak ditemukan.";
    }
} else {
    echo "Invalid request.";
}
