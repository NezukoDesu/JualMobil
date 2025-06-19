<?php
session_start();
include('../DB.php');

// Hanya untuk Super Admin dan Manager
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'Super Admin' && $_SESSION['role'] !== 'Manager')) {
    header("Location: ../Login.php");
    exit;
}

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Hitung total data
$totalQuery = "SELECT COUNT(*) AS total FROM requests";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalData = $totalRow['total'];
$totalPages = ceil($totalData / $limit);

// Ambil data sesuai halaman
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
    ORDER BY r.createdAt DESC
    LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemesanan - JualMobil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .status-badge {
            @apply px-3 py-1.5 rounded-full text-sm font-medium inline-flex items-center gap-1.5;
        }
        .status-menunggu {
            @apply bg-yellow-100 text-yellow-800 border border-yellow-200;
        }
        .status-disetujui {
            @apply bg-emerald-100 text-emerald-800 border border-emerald-200;
        }
        .status-ditolak {
            @apply bg-red-100 text-red-800 border border-red-200;
        }
        .action-btn {
            @apply px-3 py-1.5 rounded-lg text-white text-sm font-medium transition-colors duration-200 inline-flex items-center gap-1.5;
        }
        .status-text {
            @apply flex items-center gap-2 text-sm font-medium;
        }
    </style>
</head>
<body class="bg-gray-50 admin-page">
    <?php include('../Layouts/navbar.php'); ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="bg-white/90 backdrop-blur-md rounded-lg shadow-xl p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Laporan Pemesanan</h2>
                    <p class="text-gray-600 mt-1">Kelola data pemesanan mobil</p>
                </div>
                
                <div class="flex items-center gap-4">
                    <!-- Filter Status -->
                    <select id="statusFilter" class="border border-gray-300 rounded-lg px-4 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700">
                        <option value="">Semua Status</option>
                        <option value="Menunggu">Menunggu</option>
                        <option value="Disetujui">Disetujui</option>
                        <option value="Ditolak">Ditolak</option>
                    </select>

                    <!-- Export Button -->
                    <a href="ExportLaporan.php" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-download mr-2"></i>Export PDF
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mobil</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <?php if ($_SESSION['role'] === 'Super Admin'): ?>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if ($result && mysqli_num_rows($result) > 0):
                            $no = $offset + 1;
                            while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="hover:bg-gray-50 status-row" data-status="<?= $row['status'] ?>">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $no++ ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('d/m/Y H:i', strtotime($row['createdAt'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= $row['customerName'] ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900"><?= $row['mobilNama'] ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">Rp <?= number_format($row['mobilHarga'], 0, ',', '.') ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="status-badge <?= strtolower($row['status']) ?>-status">
                                        <?php if ($row['status'] === 'Menunggu'): ?>
                                            <i class="fas fa-clock"></i>
                                        <?php elseif ($row['status'] === 'Disetujui'): ?>
                                            <i class="fas fa-check-circle"></i>
                                        <?php else: ?>
                                            <i class="fas fa-times-circle"></i>
                                        <?php endif; ?>
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                                <?php if ($_SESSION['role'] === 'Super Admin'): ?>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($row['status'] === 'Menunggu'): ?>
                                        <div class="flex gap-2">
                                            <button onclick="updateStatus(<?= $row['id'] ?>, 'Disetujui')" 
                                                    class="action-btn bg-emerald-600 hover:bg-emerald-700">
                                                <i class="fas fa-check"></i>
                                                Setuju
                                            </button>
                                            <button onclick="updateStatus(<?= $row['id'] ?>, 'Ditolak')" 
                                                    class="action-btn bg-red-600 hover:bg-red-700">
                                                <i class="fas fa-times"></i>
                                                Tolak
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <span class="status-text <?= $row['status'] === 'Disetujui' ? 'text-emerald-600' : 'text-red-600' ?>">
                                            <?php if ($row['status'] === 'Disetujui'): ?>
                                                <i class="fas fa-check-circle"></i>
                                                <span>Disetujui oleh <?= htmlspecialchars($_SESSION['username']) ?></span>
                                            <?php else: ?>
                                                <i class="fas fa-times-circle"></i>
                                                <span>Ditolak oleh <?= htmlspecialchars($_SESSION['username']) ?></span>
                                            <?php endif; ?>
                                            <span class="text-gray-400 text-xs">
                                                (<?= date('d/m/Y H:i', strtotime($row['updatedAt'])) ?>)
                                            </span>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; 
                        else: ?>
                            <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data pemesanan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="flex justify-center mt-6 gap-2">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>" 
                       class="px-4 py-2 text-sm rounded-lg transition-colors duration-200 
                              <?= $i == $page 
                                  ? 'bg-blue-600 text-white' 
                                  : 'bg-gray-100 text-gray-700 hover:bg-blue-50' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>

            <!-- Back Button -->
            <div class="mt-6">
                <a href="../Index.php" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    <script>
        // Filter status
        document.getElementById('statusFilter').addEventListener('change', function () {
            const status = this.value;
            const rows = document.querySelectorAll('.status-row');
            rows.forEach(row => {
                row.style.display = (!status || row.getAttribute('data-status') === status) ? '' : 'none';
            });
        });

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
