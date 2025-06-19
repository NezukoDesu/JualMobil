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
    <title>Daftar Pesanan - JualMobil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 admin-page">
    <?php include('../Layouts/navbar.php'); ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="bg-white/90 backdrop-blur-md rounded-lg shadow-xl p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Daftar Pesanan Saya</h2>
                    <p class="text-gray-600 mt-1">Riwayat pemesanan mobil Anda</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Mobil</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        if ($result->num_rows > 0) {
                            $no = 1;
                            while ($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4"><?= $no++ ?></td>
                                    <td class="px-6 py-4"><?= $row['mobilNama'] ?></td>
                                    <td class="px-6 py-4">Rp <?= number_format($row['mobilHarga'], 0, ',', '.') ?></td>
                                    <td class="px-6 py-4"><?= date('d/m/Y H:i', strtotime($row['createdAt'])) ?></td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-sm font-medium
                                            <?php echo match($row['status']) {
                                                'Disetujui' => 'bg-green-100 text-green-800',
                                                'Ditolak' => 'bg-red-100 text-red-800',
                                                default => 'bg-yellow-100 text-yellow-800'
                                            } ?>">
                                            <?= $row['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile;
                        } else {
                            echo "<tr><td colspan='5' class='px-6 py-4 text-center text-gray-500'>Belum ada pesanan.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
