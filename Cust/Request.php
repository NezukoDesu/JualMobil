<?php
include '../DB.php';
session_start();

// Security check
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Customer') {
    header("Location: ../Login.php");
    exit;
}

// Get data user id
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param('s', $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$userId = $user['id'];

// get semua data request dengan detail
$query = "SELECT 
    r.id,
    r.status,
    r.createdAt,
    m.nama AS mobilNama,
    m.harga AS mobilHarga,
    m.stok AS mobilStok
    FROM requests r
    JOIN mobil m ON r.itemId = m.id
    WHERE r.userId = ?
    ORDER BY r.createdAt DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesanan</title>
    <!-- Link ke Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-disetujui {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .status-ditolak {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .status-menunggu {
            background-color: #ffc107;
            color: black;
            padding: 5px 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Daftar Pesanan</h2>
        <table class="table table-bordered">
            <thead>
                <tr class="table-primary">
                    <th>No.</th>
                    <th>Nama Mobil</th>
                    <th>Harga</th>
                    <th>Tanggal Pemesanan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $no = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$no}</td>";
                        echo "<td>{$row['mobilNama']}</td>";
                        echo "<td>" . number_format($row['mobilHarga'], 0, ',', '.') . "</td>";
                        echo "<td>{$row['createdAt']}</td>";
                        echo "<td>";
                        if ($row['status'] == 'Disetujui') {
                            echo "<span class='status-disetujui'>Disetujui</span>";
                        } elseif ($row['status'] == 'Ditolak') {
                            echo "<span class='status-ditolak'>Ditolak</span>";
                        } else {
                            echo "<span class='status-menunggu'>Menunggu</span>";
                        }
                        echo "</td>";
                        echo "</tr>";
                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>Tidak ada data.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <div class="text-center mt-4">
            <a href="../index.php" class="btn btn-secondary">← Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>
