<?php
include '../DB.php'; // Menyertakan koneksi database
session_start();

// Periksa sesi dan role pengguna
if (!isset($_SESSION['role'])) {
    echo "Access denied! Redirecting to login...";
    header("Location: /JualMobil/Login.php"); // Ganti dengan URL halaman login Anda
    exit;
}

// Query database untuk mendapatkan data dari tabel `requests`
$query = "SELECT r.*, m.nama AS mobilNama FROM requests r JOIN mobil m ON r.itemId = m.id WHERE r.userId = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $_SESSION['userId']);
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
                    echo "<tr><td colspan='4' class='text-center'>Tidak ada data.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
