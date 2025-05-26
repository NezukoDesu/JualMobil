<?php
session_start();
include('../DB.php');

if (!isset($_SESSION['username'])) {
    header("Location: ../Login.php");
    exit;
}

if ($_SESSION['role'] !== 'Super Admin' && $_SESSION['role'] !== 'Manager') {
    header("Location: ../Index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['aksi'] === 'hapus_user') {
    if ($_SESSION['role'] !== 'Super Admin') {
        echo 'error: Unauthorized';
        exit;
    }

    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);

    echo $stmt->execute() ? 'success' : 'error: ' . $stmt->error;
    exit;
}

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Total data
$totalQuery = "SELECT COUNT(*) as total FROM users";
$totalResult = mysqli_query($conn, $totalQuery);
$totalData = mysqli_fetch_assoc($totalResult)['total'];
$totalPages = ceil($totalData / $limit);

// Ambil data per halaman dan urutkan dari terbaru
$query = "SELECT * FROM users ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data User</title>
    <link rel="stylesheet" href="../Style/style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .header h2 {
            text-align: center;
        }

        .styled-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .styled-table thead {
            background-color: #f3f3f3;
        }

        .styled-table th, .styled-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #ddd;
        }

        .btn {
            text-decoration: none;
            padding: 4px 8px;
            border-radius: 5px;
        }

        .btn.edit {
            color: #ffc107;
        }

        .btn.delete {
            color: #dc3545;
        }

        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 4px;
            background-color: #007bff;
            color: white;
            border-radius: 6px;
            text-decoration: none;
        }

        .pagination a.active {
            background-color: #0056b3;
        }

        .back-btn {
            display: block;
            width: 300px;
            margin: 30px auto 0;
            text-align: center;
            background-color: #2563eb;
            color: white;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
        }

        .back-btn:hover {
            background-color: #1e40af;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>DAFTAR PENGGUNA</h2>
        <p>Selamat datang, <?= htmlspecialchars($_SESSION['username']); ?>!</p>
    </div>

    <table class="styled-table">
        <thead>
            <tr>
                <th style="text-align:left">No</th>
                <th style="text-align:left">Username</th>
                <th style="text-align:left">Role</th>
                <th style="text-align:left">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = $offset + 1; while ($user = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($user['username']); ?></td>
                    <td><?= htmlspecialchars($user['role']); ?></td>
                    <td>
                        <a href="EditRole.php?id=<?= urlencode($user['id']); ?>" class="btn edit">✏️</a>
                        <?php if ($_SESSION['role'] === 'Super Admin'): ?>
                            <a href="#" class="btn delete delete-user" data-id="<?= $user['id']; ?>">🗑️</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Pagination hanya jika data lebih dari 10 -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

    <a href="../Index.php" class="back-btn">← Kembali ke Halaman Utama</a>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).on('click', '.delete-user', function(e) {
    e.preventDefault();
    if (confirm('Yakin ingin menghapus?')) {
        const id = $(this).data('id');
        $.post('', { aksi: 'hapus_user', id: id }, function(res) {
            if (res.trim() === 'success') {
                location.reload();
            } else {
                alert('Gagal menghapus: ' + res);
            }
        }).fail(function() {
            alert('Terjadi kesalahan saat menghapus.');
        });
    }
});
</script>

</body>
</html>
