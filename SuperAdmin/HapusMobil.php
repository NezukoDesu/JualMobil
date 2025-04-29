<?php
session_start();
include '../DB.php';

// Pastikan hanya yang login dapat mengakses
if (!isset($_SESSION['username'])) {
    echo 'unauthorized';
    exit;
}

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Query untuk menghapus data mobil berdasarkan ID
    $query = "DELETE FROM mobil WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        echo 'failed_to_prepare';
        exit; // Jika gagal menyiapkan query
    }

    mysqli_stmt_bind_param($stmt, 'i', $id);

    if (mysqli_stmt_execute($stmt)) {
        echo 'success';
    } else {
        // Mengambil alasan kegagalan dari MySQL
        $error_message = mysqli_error($conn);
        echo 'fail: ' . $error_message;
    }
} else {
    echo 'fail: Missing ID';
}
?>
