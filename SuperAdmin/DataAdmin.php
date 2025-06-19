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
    <title>Data User - JualMobil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <?php include('../Layouts/navbar.php'); ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Data Pengguna</h2>
                <div class="flex gap-2">
                    <a href="ExportDataAdmin.php" 
                       class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        <i class="fas fa-download mr-2"></i>Export PDF
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php 
                        $no = $offset + 1; 
                        while ($user = mysqli_fetch_assoc($result)): 
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap"><?= $no++; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img src="../Uploads/<?= $user['foto'] ?? 'Foto/Default.png' ?>" 
                                         class="h-8 w-8 rounded-full mr-3">
                                    <?= htmlspecialchars($user['username']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-sm rounded-full <?= 
                                    match($user['role']) {
                                        'Super Admin' => 'bg-red-100 text-red-800',
                                        'Manager' => 'bg-blue-100 text-blue-800',
                                        'Sales' => 'bg-green-100 text-green-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    }
                                ?>">
                                    <?= htmlspecialchars($user['role']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="EditRole.php?id=<?= $user['id'] ?>" 
                                   class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($_SESSION['role'] === 'Super Admin'): ?>
                                <button onclick="deleteUser(<?= $user['id'] ?>)" 
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="flex justify-center mt-6 gap-2">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>" 
                       class="px-4 py-2 text-sm <?= $i == $page 
                           ? 'bg-blue-600 text-white' 
                           : 'bg-gray-100 text-gray-800' ?> rounded-lg hover:bg-blue-500 hover:text-white">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function deleteUser(id) {
        if (confirm('Yakin ingin menghapus pengguna ini?')) {
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `aksi=hapus_user&id=${id}`
            })
            .then(response => response.text())
            .then(result => {
                if (result.trim() === 'success') {
                    location.reload();
                } else {
                    alert('Gagal menghapus pengguna');
                }
            });
        }
    }
    </script>
</body>
</html>
