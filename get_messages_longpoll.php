<?php
session_start();
include '../DB.php';

if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'customer') {
    http_response_code(401);
    exit;
}

$customerId = $_SESSION['id'];
$salesId = isset($_GET['to']) ? (int)$_GET['to'] : 0;
$lastId = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

if (!$salesId) {
    echo json_encode([]);
    exit;
}

set_time_limit(30); // Max 30 detik tahan request

$start = time();
while (true) {
    $query = "
        SELECT m.*, u.username AS sender_name
        FROM messages m
        JOIN users u ON m.senderId = u.id
        WHERE ((m.senderId = ? AND m.receiverId = ?) OR (m.senderId = ? AND m.receiverId = ?))
          AND m.id > ?
        ORDER BY m.createdAt ASC
    ";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iiiii", $customerId, $salesId, $salesId, $customerId, $lastId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $messages = [];
    if ($result) {
        $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    if (count($messages) > 0) {
        header('Content-Type: application/json');
        echo json_encode($messages);
        break;
    }

    // Kalau sudah 25 detik gak ada pesan baru, kirim empty response supaya client retry
    if ((time() - $start) > 25) {
        header('Content-Type: application/json');
        echo json_encode([]);
        break;
    }

    // Tidur sebentar sebelum cek lagi
    usleep(500000); // 0.5 detik
}
