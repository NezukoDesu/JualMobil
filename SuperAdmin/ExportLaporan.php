<?php
session_start();
include('../DB.php');

// Cek role
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'Super Admin' && $_SESSION['role'] !== 'Manager')) {
    header("Location: ../Login.php");
    exit;
}

// Load library TCPDF
require_once('../tcpdf/tcpdf.php'); // pastikan path ke TCPDF benar

// Query semua data pemesanan (tanpa filter bulan)
$query = "SELECT 
    r.id,
    r.status,
    r.createdAt,
    r.updatedAt,
    m.nama AS mobilNama,
    m.harga AS mobilHarga,
    u.username AS customerName
    FROM requests r
    JOIN mobil m ON r.itemId = m.id
    JOIN users u ON r.userId = u.id
    ORDER BY r.createdAt DESC";

$result = mysqli_query($conn, $query);

// Buat PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('JualMobil');
$pdf->SetTitle('Laporan Pemesanan Keseluruhan');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

// Judul
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Laporan Seluruh Pemesanan', 0, 1, 'C');

// Table header
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Ln(4);
$pdf->Cell(10, 8, 'No', 1, 0, 'C');
$pdf->Cell(30, 8, 'Tanggal', 1, 0, 'C');
$pdf->Cell(35, 8, 'Customer', 1, 0, 'C');
$pdf->Cell(35, 8, 'Mobil', 1, 0, 'C');
$pdf->Cell(30, 8, 'Harga', 1, 0, 'C');
$pdf->Cell(40, 8, 'Status', 1, 1, 'C');

// Table body
$pdf->SetFont('helvetica', '', 10);
$no = 1;

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pdf->Cell(10, 8, $no++, 1, 0, 'C');
        $pdf->Cell(30, 8, date('d/m/Y', strtotime($row['createdAt'])), 1, 0, 'C');
        $pdf->Cell(35, 8, $row['customerName'], 1, 0);
        $pdf->Cell(35, 8, $row['mobilNama'], 1, 0);
        $pdf->Cell(30, 8, 'Rp ' . number_format($row['mobilHarga'], 0, ',', '.'), 1, 0);
        $pdf->Cell(40, 8, $row['status'], 1, 1);
    }
} else {
    $pdf->Cell(180, 8, 'Tidak ada data pemesanan.', 1, 1, 'C');
}

// Output PDF
$pdf->Output('Laporan_Seluruh_Pemesanan.pdf', 'I');
?>
