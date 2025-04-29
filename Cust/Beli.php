<?php
include '../DB.php'; // Koneksi ke database
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    echo "Access denied! Redirecting to login...";
    header("Location: /JualMobil/Login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Mobil tidak ditemukan!";
    exit;
}

$mobilId = $_GET['id'];
$query = "SELECT * FROM mobil WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $mobilId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Mobil tidak ditemukan!";
    exit;
}

$mobil = $result->fetch_assoc();

// Proses pengajuan pemesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['id']; 
    $status = 'Menunggu';
    $createdAt = date('Y-m-d H:i:s');
    $updatedAt = date('Y-m-d H:i:s');

    $insertQuery = "INSERT INTO requests (itemId, userid, status, createdAt, updatedAt) VALUES (?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param('iisss', $mobilId, $userId, $status, $createdAt, $updatedAt);

    if ($insertStmt->execute()) {
        echo "<script>alert('Pesanan berhasil diajukan!'); window.location.href = '/JualMobil/Customer/request.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat memproses pesanan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Mobil</title>
    <!-- Link ke Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5 flex justify-center items-center h-full">
        <div class="bg-white rounded shadow p-4 relative" style="width: 500px; height: 500px;">
        <div class="p-8" style="width: 400px; height: 200px; margin: 0 auto;">
            <!-- Gambar -->
            <img style="width: 400px; height:200px; object-fit: cover;" src="/JualMobil/uploads/<?= htmlspecialchars($mobil['gambar']) ?>" alt="<?= htmlspecialchars($mobil['nama']) ?>" class="w-full h-48 object-cover rounded mb-4">

            <!-- Info Mobil -->
            <strong style="font-size:larger;">Nama : </strong><h5 style="font-size:larger;" class="text-xl font-semibold text-left"><?= htmlspecialchars($mobil['nama'] ?? 'Nama tidak tersedia'); ?></h5>
            <p  style="font-size:larger ;"class="text-sm text-gray-600 mb-1 text-left">
            <strong>Stok : </strong> <?= htmlspecialchars($mobil['stok'] ?? 'Tidak tersedia'); ?>
            </p>
            <p style="font-size:larger ;" class="text-sm text-gray-600 mb-1 text-left">
            <strong >Harga : </strong> Rp <?= number_format($mobil['harga'] ?? 0, 0, ',', '.'); ?>
            </p>
            <strong style="font-size:larger ;">Keterangan :</strong><br><p style="font-size:larger ;" class="text-sm text-gray-600 mb-4 text-left"><?= nl2br(htmlspecialchars($mobil['keterangan'] ?? 'Tidak ada keterangan')); ?></p>


            <!-- Action Buttons -->
            <div class="flex justify-center mt-4">
                <form method="POST" class="flex flex-col items-center gap-2">
                    <button type="submit" class="btn btn-primary w-full">Beli Mobil</button>
                    <a href="../Index.php" class="btn btn-secondary w-full">Kembali</a>
                </form>
            </div>
        </div>
        </div>
    </div>
</body>

</html>
