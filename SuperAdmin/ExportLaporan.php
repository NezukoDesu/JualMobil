<?php
session_start();
include('../DB.php');
require_once('../utils/PDFGenerator.php');

if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'Super Admin' && $_SESSION['role'] !== 'Manager')) {
    exit('Unauthorized access');
}

$pdf = new PDFGenerator('L'); 
$pdf->SetMargins(15, 15, 15);
$pdf->SetCreator('JualMobil System');
$pdf->SetAuthor($_SESSION['username']);
$pdf->SetTitle('Laporan Pemesanan');

$pdf->AddPage();

$pdf->SetFont('helvetica', '', 11);
$pdf->Cell(0, 5, 'Periode: '.date('d/m/Y'), 0, 1, 'C');
$pdf->Ln(10);

$header = array('No.', 'Tanggal', 'Customer', 'Mobil', 'Harga', 'Status');
$widths = array(20, 35, 50, 85, 45, 35);

// Get data
$query = "SELECT 
    r.id,
    r.status,
    r.createdAt,
    m.nama AS mobilNama,
    m.harga AS mobilHarga,
    u.username AS customerName
    FROM requests r
    JOIN mobil m ON r.itemId = m.id
    JOIN users u ON r.userId = u.id
    ORDER BY r.createdAt DESC";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$data = array();
$no = 1;

while($row = mysqli_fetch_assoc($result)) {
    $data[] = array(
        $no++,
        date('d/m/Y H:i', strtotime($row['createdAt'])),
        $row['customerName'],
        $row['mobilNama'],
        'Rp '.number_format($row['mobilHarga'], 0, ',', '.'),
        $row['status']
    );
}

$pdf->ColoredTable($header, $data, $widths);

$pdf->Ln(15);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 6, 'Ringkasan:', 0, 1);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 6, 'Total Transaksi: '.count($data).' pemesanan', 0, 1);
$pdf->Cell(0, 6, 'Dicetak oleh: '.$_SESSION['username'], 0, 1);
$pdf->Cell(0, 6, 'Tanggal Cetak: '.date('d/m/Y H:i'), 0, 1);

// Output
$pdf->Output('Laporan_Pemesanan_'.date('Y-m-d_H-i').'.pdf', 'D');
