<?php
session_start();
include '../DB.php';

// Pastikan hanya user yang sudah login dan memiliki role yang boleh menghapus
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'Super Admin' && $_SESSION['role'] !== 'Manager')) {
    echo 'unauthorized';
    exit;
}

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $query = "DELETE FROM mobil WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        echo 'failed_to_prepare';
        exit;
    }

    mysqli_stmt_bind_param($stmt, 'i', $id);

    if (mysqli_stmt_execute($stmt)) {
        echo 'success';
    } else {
        echo 'fail: ' . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
} else {
    echo 'fail: Missing ID';
}
?>
