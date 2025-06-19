<?php
require_once('tcpdf/tcpdf.php');

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('JualMobil');
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Test PDF');

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 12);

// Add content
$pdf->Cell(0, 10, 'TCPDF berhasil diinstall!', 0, 1, 'C');

// Output PDF
$pdf->Output('test.pdf', 'D');
