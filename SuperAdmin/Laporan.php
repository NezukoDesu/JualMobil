<?php
session_start();
include('../DB.php');
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'Super Admin' && $_SESSION['role'] !== 'Manager')) {
    header("Location: ../Login.php");
    exit;
}

// get semua data request dengan detail
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pemesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .status-disetujui { background-color: #28a745; color: white; padding: 5px 10px; border-radius: 5px; }
        .status-ditolak { background-color: #dc3545; color: white; padding: 5px 10px; border-radius: 5px; }
        .status-menunggu { background-color: #ffc107; color: black; padding: 5px 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Laporan Pemesanan Mobil</h2>
        
        <!-- Filter  status dipesanan -->
        <div class="mb-3">
            <select class="form-select w-25" id="statusFilter">
                <option value="">Semua Status</option>
                <option value="Menunggu">Menunggu</option>
                <option value="Disetujui">Disetujui</option>
                <option value="Ditolak">Ditolak</option>
            </select>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr class="table-primary">
                    <th>No.</th>
                    <th>Tanggal Pesan</th>
                    <th>Customer</th>
                    <th>Nama Mobil</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <?php if ($_SESSION['role'] === 'Super Admin'): ?>
                    <th>Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && mysqli_num_rows($result) > 0) {
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr class='status-row' data-status='{$row['status']}'>";
                        echo "<td>{$no}</td>";
                        echo "<td>" . date('d/m/Y H:i', strtotime($row['createdAt'])) . "</td>";
                        echo "<td>{$row['customerName']}</td>";
                        echo "<td>{$row['mobilNama']}</td>";
                        echo "<td>Rp " . number_format($row['mobilHarga'], 0, ',', '.') . "</td>";
                        echo "<td>";
                        $statusClass = match($row['status']) {
                            'Disetujui' => 'status-disetujui',
                            'Ditolak' => 'status-ditolak',
                            default => 'status-menunggu'
                        };
                        echo "<span class='{$statusClass}'>{$row['status']}</span>";
                        echo "</td>";
                        
                        if ($_SESSION['role'] === 'Super Admin') {
                            echo "<td>";
                            if ($row['status'] === 'Menunggu') {
                                echo "<button onclick='updateStatus({$row['id']}, \"Disetujui\")' class='btn btn-success btn-sm me-2'>Setuju</button>";
                                echo "<button onclick='updateStatus({$row['id']}, \"Ditolak\")' class='btn btn-danger btn-sm'>Tolak</button>";
                            } else {
                                // tampilkan note status pesanan
                                if ($row['status'] === 'Disetujui') {
                                    echo "<span class='text-success'><i class='bi bi-check-circle'></i> Disetujui oleh Admin</span>";
                                } else if ($row['status'] === 'Ditolak') {
                                    echo "<span class='text-danger'><i class='bi bi-x-circle'></i> Ditolak oleh Admin</span>";
                                }
                            }
                            echo "</td>";
                        }
                        
                        echo "</tr>";
                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>Tidak ada data pemesanan.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="../Index.php" class="btn btn-secondary">Kembali</a>
    </div>

    <script>
        document.getElementById('statusFilter').addEventListener('change', function() {
            const status = this.value;
            const rows = document.querySelectorAll('.status-row');
            
            rows.forEach(row => {
                if (!status || row.getAttribute('data-status') === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Update status
        function updateStatus(requestId, newStatus) {
            if (confirm(`Apakah Anda yakin ingin ${newStatus === 'Disetujui' ? 'menyetujui' : 'menolak'} pesanan ini?`)) {
                fetch('UpdateStatus.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${requestId}&status=${newStatus}`
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        alert('Status berhasil diperbarui');
                        location.reload();
                    } else {
                        alert('Gagal memperbarui status');
                    }
                });
            }
        }
    </script>
</body>
</html>
