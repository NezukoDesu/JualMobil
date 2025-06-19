<?php
session_start();
include('../DB.php');
require_once('../utils/PDFGenerator.php');

// Cek hak akses
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'Super Admin' && $_SESSION['role'] !== 'Manager')) {
    exit('Unauthorized access');
}

// Jika belum ada bulan & tahun, tampilkan form filter
if (!isset($_GET['bulan']) || !isset($_GET['tahun'])) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Filter Laporan</title>
        <style>
            body {
                font-family: 'Segoe UI', sans-serif;
                background: #f0f2f5;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }

            .card {
                background: #fff;
                padding: 30px 40px;
                border-radius: 12px;
                box-shadow: 0 8px 16px rgba(0,0,0,0.1);
                width: 100%;
                max-width: 400px;
            }

            h2 {
                margin-top: 0;
                margin-bottom: 20px;
                text-align: center;
                color: #2c3e50;
            }

            label {
                display: block;
                margin-bottom: 6px;
                font-weight: bold;
            }

            select {
                width: 100%;
                padding: 10px;
                margin-bottom: 20px;
                border-radius: 8px;
                border: 1px solid #ccc;
            }

            button {
                width: 100%;
                padding: 12px;
                background-color: #2c3e50;
                border: none;
                border-radius: 8px;
                color: white;
                font-weight: bold;
                cursor: pointer;
            }

            button:hover {
                background-color: #1a252f;
            }
        </style>
    </head>
    <body>
        <div class="card">
            <h2>Export Laporan Pemesanan</h2>
            <form method="GET" action="">
                <label for="bulan">Pilih Bulan:</label>
                <select name="bulan" id="bulan" required>
                    <?php
                    for ($i = 1; $i <= 12; $i++) {
                        echo "<option value='$i'>" . date('F', mktime(0, 0, 0, $i, 1)) . "</option>";
                    }
                    ?>
                </select>

                <label for="tahun">Pilih Tahun:</label>
                <select name="tahun" id="tahun" required>
                    <?php
                    $now = date('Y');
                    for ($y = $now; $y >= $now - 5; $y--) {
                        echo "<option value='$y'>$y</option>";
                    }
                    ?>
                </select>

                <button type="submit">Export PDF</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Jika sudah pilih bulan dan tahun, lanjut buat PDF
$bulan = str_pad($_GET['bulan'], 2, '0', STR_PAD_LEFT);
$tahun = $_GET['tahun'];

$pdf = new PDFGenerator('L');
$pdf->SetMargins(15, 15, 15);
$pdf->SetCreator('JualMobil System');
$pdf->SetAuthor($_SESSION['username']);
$pdf->SetTitle("Laporan Pemesanan Bulan $bulan Tahun $tahun");

$pdf->AddPage();
$pdf->SetFont('helvetica', '', 11);
$pdf->Cell(0, 5, "Periode: Bulan $bulan Tahun $tahun", 0, 1, 'C');
$pdf->Ln(10);

$header = array('No.', 'Tanggal', 'Customer', 'Mobil', 'Harga', 'Status');
$widths = array(20, 35, 50, 85, 45, 35);

// Ambil data dari DB
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
    WHERE MONTH(r.createdAt) = '$bulan' AND YEAR(r.createdAt) = '$tahun'
    ORDER BY r.createdAt DESC";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}

$data = array();
$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = array(
        $no++,
        date('d/m/Y H:i', strtotime($row['createdAt'])),
        $row['customerName'],
        $row['mobilNama'],
        'Rp ' . number_format($row['mobilHarga'], 0, ',', '.'),
        $row['status']
    );
}

$pdf->ColoredTable($header, $data, $widths);

$pdf->Ln(15);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 6, 'Ringkasan:', 0, 1);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 6, 'Total Transaksi: ' . count($data) . ' pemesanan', 0, 1);
$pdf->Cell(0, 6, 'Dicetak oleh: ' . $_SESSION['username'], 0, 1);
$pdf->Cell(0, 6, 'Tanggal Cetak: ' . date('d/m/Y H:i'), 0, 1);

// Output PDF
$pdf->Output("Laporan_Pemesanan_{$tahun}_{$bulan}.pdf", 'D');
