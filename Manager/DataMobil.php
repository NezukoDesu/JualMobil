<?php
session_start();
include('../DB.php');

// Akses hanya untuk Super Admin dan Manager
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'Super Admin' && $_SESSION['role'] !== 'Manager')) {
    header("Location: ../Login.php");
    exit;
}

// Pagination setup
$limit = 10; // maksimal 10 mobil per halaman
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Total data
$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM mobil");
$totalRow = mysqli_fetch_assoc($totalQuery);
$totalMobil = $totalRow['total'];
$totalPages = ceil($totalMobil / $limit);

// Ambil data mobil untuk halaman sekarang
$query = "SELECT * FROM mobil ORDER BY id ASC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Mobil - JualMobil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <?php include('../Layouts/navbar.php'); ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Data Mobil</h2>
                <a href="../SuperAdmin/TambahMobil.php" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i>Tambah Mobil
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Mobil</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        if ($result && mysqli_num_rows($result) > 0) {
                            $no = $offset + 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                $shortDesc = strlen($row['keterangan']) > 20 ? substr($row['keterangan'], 0, 20) . '...' : $row['keterangan'];
                                echo "<tr id='row-{$row['id']}'>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>{$no}</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>{$row['nama']}</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>{$row['stok']}</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>{$shortDesc}</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>
                                        <a href='../SuperAdmin/EditMobil.php?id={$row['id']}' class='bg-yellow-500 text-white px-3 py-1 rounded-lg hover:bg-yellow-600 transition'>Edit</a>
                                        <button onclick='hapusMobil({$row['id']})' class='bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 transition'>Hapus</button>
                                      </td>";
                                echo "</tr>";
                                $no++;
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>Tidak ada data mobil.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="flex justify-center mt-6 gap-2">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>" 
                       class="px-4 py-2 text-sm <?= $i == $page 
                           ? 'bg-blue-600 text-white' 
                           : 'bg-gray-100 text-gray-800' ?> rounded-lg hover:bg-blue-500 hover:text-white transition">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function hapusMobil(id) {
        if (confirm('Yakin ingin menghapus mobil ini?')) {
            fetch('../SuperAdmin/HapusMobil.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + encodeURIComponent(id)
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === 'success') {
                    alert('Data mobil berhasil dihapus.');
                    const row = document.getElementById('row-' + id);
                    if (row) row.remove();
                } else {
                    alert('Gagal menghapus data: ' + data);
                }
            })
            .catch(err => alert('Error: ' + err));
        }
    }
    </script>
</body>
</html>
