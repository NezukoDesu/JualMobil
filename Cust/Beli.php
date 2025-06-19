<?php
include '../DB.php';
session_start();

// Cek dulu, user udah login belum dan apakah dia role-nya "Customer"
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Customer') {
    // Kalau belum login atau bukan customer, langsung tendang ke halaman login
    header("Location: ../Login.php");
    exit;
}

// Ambil ID user berdasarkan username yang login
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param('s', $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$userId = $user['id'];

// Cek ID mobil 
if (!isset($_GET['id'])) {
    echo "Mobil tidak ditemukan!";
    exit;
}

$mobilId = $_GET['id'];
$query = "SELECT * FROM mobil WHERE id = ? AND stok > 0"; // Ambil mobil yang masih ready
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $mobilId);
$stmt->execute();
$result = $stmt->get_result();

// Kalau mobilnya gak ada atau stoknya 0
if ($result->num_rows === 0) {
    echo "Mobil tidak tersedia!";
    exit;
}

$mobil = $result->fetch_assoc();

// Kalau form dibuka lewat tombol submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cek dulu apakah user udah pernah ngajuin request buat mobil ini dan masih nunggu
    $checkQuery = "SELECT id FROM requests WHERE userId = ? AND itemId = ? AND status = 'Menunggu'";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param('ii', $userId, $mobilId);
    $checkStmt->execute();
    
    if ($checkStmt->get_result()->num_rows > 0) {
        // Udah pernah request, kasih alert
        echo "<script>alert('Anda sudah memiliki pengajuan yang menunggu untuk mobil ini!');</script>";
    } else {
        // Kalau belum pernah, simpan request baru ke database
        $insertQuery = "INSERT INTO requests (itemId, userId, status) VALUES (?, ?, 'Menunggu')";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param('ii', $mobilId, $userId);

        if ($insertStmt->execute()) {
            // Kalau sukses, kasih alert dan arahkan ke halaman request
            echo "<script>
                alert('Pesanan berhasil diajukan!'); 
                window.location.href = 'Request.php';
            </script>";
        } else {
            // Kalau gagal, kasih alert error
            echo "<script>alert('Terjadi kesalahan saat memproses pesanan!');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesan Mobil - JualMobil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 admin-page">
    <?php include('../Layouts/navbar.php'); ?>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-xl p-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800">Detail Pemesanan</h2>
                <p class="text-gray-600 mt-2">Review detail mobil sebelum mengajukan pesanan</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Image Section -->
                <div>
                    <img src="../uploads/<?= htmlspecialchars($mobil['gambar']) ?>" 
                         alt="<?= htmlspecialchars($mobil['nama']) ?>"
                         class="w-full h-[400px] object-cover rounded-lg shadow-md">
                </div>

                <!-- Details Section -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($mobil['nama']) ?></h3>
                        <div class="mt-2 space-y-2">
                            <p class="text-3xl font-bold text-blue-600">
                                Rp<?= number_format($mobil['harga'], 0, ',', '.') ?>
                            </p>
                            <p class="inline-flex items-center text-sm text-gray-600">
                                <i class="fas fa-car mr-2"></i>
                                Stok: <span class="font-semibold ml-1"><?= $mobil['stok'] ?> unit</span>
                            </p>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold text-gray-700 mb-2">Keterangan</h4>
                        <p class="text-gray-600"><?= htmlspecialchars($mobil['keterangan']) ?></p>
                    </div>

                    <form method="POST" class="pt-4">
                        <button type="submit" 
                                class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Ajukan Pesanan
                        </button>
                    </form>

                    <a href="Request.php" 
                       class="block text-center mt-4 text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Daftar Pesanan
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


