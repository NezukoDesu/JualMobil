<?php
session_start();
include('../DB.php');
require_once('../utils/PDFGenerator.php');

if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'Super Admin' && $_SESSION['role'] !== 'Manager')) {
    exit('Unauthorized access');
}

$pdf = new PDFGenerator('P', 'mm', 'A4');
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 15);

$pdf->AddPage();

$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'LAPORAN DATA ADMIN', 0, 1, 'C');
$pdf->Ln(5);
$header = array('No.', 'Username', 'Email', 'Role', 'Status');
$widths = array(15, 45, 60, 35, 25);

// Get data
$query = "SELECT * FROM users ORDER BY role, username";
$result = mysqli_query($conn, $query);
$data = array();
$no = 1;

while($row = mysqli_fetch_assoc($result)) {
    $data[] = array(
        $no++,
        $row['username'],
        $row['email'],
        $row['role'],
        'Aktif'
    );
}

$pdf->ColoredTable($header, $data, $widths);

// Output
$pdf->Output('Data_Admin_'.date('Y-m-d_H-i').'.pdf', 'D');
