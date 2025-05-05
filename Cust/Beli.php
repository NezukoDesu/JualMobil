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
    <title>Ajukan Mobil</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .mobil-info {
            margin: 20px 0;
        }
        .mobil-info img {
            width: 100%;
            max-width: 400px;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
            display: block;
            margin: 0 auto 15px auto;
        }
        .mobil-info p {
            margin: 8px 0;
            font-size: 16px;
        }
        .btn-submit {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #555;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Ajukan Permintaan Mobil</h2>

        <div class="mobil-info">
            <img src="../uploads/<?= htmlspecialchars($mobil['gambar']) ?>" alt="<?= htmlspecialchars($mobil['nama']) ?>">
            <p><strong>Nama Mobil:</strong> <?= htmlspecialchars($mobil['nama']) ?></p>
            <p><strong>Stok Tersedia:</strong> <?= $mobil['stok'] ?></p>
            <p><strong>Harga:</strong> Rp<?= number_format($mobil['harga'], 0, ',', '.') ?></p>
            <p><strong>Keterangan:</strong> <?= htmlspecialchars($mobil['keterangan']) ?></p>
        </div>

        <form method="POST">
            <button type="submit" class="btn-submit">Ajukan Sekarang</button>
        </form>

        <a href="Request.php" class="back-link">← Kembali ke Daftar Permintaan</a>
    </div>
</body>
</html>


