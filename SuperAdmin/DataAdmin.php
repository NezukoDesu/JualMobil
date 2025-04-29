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
    // Hanya Super Admin yang bisa menghapus
    if ($_SESSION['role'] !== 'Super Admin') {
        echo 'error: Unauthorized';
        exit;
    }

    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error: ' . $stmt->error;
    }
    exit;
}

$query = "SELECT * FROM users ORDER BY id ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            margin-bottom: 10px;
            font-size: 24px;
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
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .styled-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .btn {
            text-decoration: none;
            padding: 4px 8px;
            margin-right: 5px;
            border-radius: 5px;
            font-size: 14px;
        }

        .btn.edit {
            color: #ffc107;
        }

        .btn.delete {
            color: #dc3545;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>DAFTAR PENGGUNA</h2>
        <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    </div>

    <table class="styled-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($user = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td>
                        <a href="EditRole.php?id=<?php echo urlencode($user['id']); ?>" class="btn edit">✏️</a>
                        <?php if ($_SESSION['role'] === 'Super Admin'): ?>
                            <a href="#" class="btn delete delete-user" data-id="<?= $user['id']; ?>">🗑️</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <button onclick="window.location.href='../Index.php'" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-300" style="width: 300px; margin-top: 25px; margin-left: 700px;">← Kembali ke Halaman Utama</button>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).on('click', '.delete-user', function(e) {
    e.preventDefault();
    if (confirm('Yakin ingin menghapus?')) {
        const id = $(this).data('id');

        $.ajax({
            url: '',
            method: 'POST',
            data: {
                aksi: 'hapus_user',
                id: id
            },
            success: function(res) {
                if (res.trim() === 'success') {
                    location.reload();
                } else {
                    alert('Gagal menghapus: ' + res);
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat menghapus.');
            }
        });
    }
});
</script>

</body>
</html>
