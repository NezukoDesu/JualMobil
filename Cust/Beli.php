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
