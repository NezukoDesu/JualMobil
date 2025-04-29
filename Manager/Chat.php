<?php
session_start();
include('../DB.php');

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ../Login.php");
    exit;
}

// Cek apakah pengguna memiliki role 'Super Admin'
if ($_SESSION['role'] !== 'Manager') {
    header("Location: ../Index.php"); // Pengguna dengan role selain 'Super Admin' diarahkan ke halaman utama
    exit;
}

// Ambil semua data pengguna dari database
$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Manager</title>
    <link rel="stylesheet" href="../Style/style.css">
</head>
<body>

<h2>Dashboard - Manager</h2>

<p>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
<p>Anda memiliki akses untuk mengedit data pengguna.</p>

<table border="1">
    <thead>
        <tr>
            <th>Username</th>
            <th>Role</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($user = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td>
                    <a href="../SuperAdmin/EditRole.php?username=<?php echo urlencode($user['username']); ?>">Edit</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<a href="../Index.php">Kembali Halaman Utama</a>
</body>
</html>
