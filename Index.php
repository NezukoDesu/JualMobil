<?php 
session_start();
include 'DB.php';

if (!isset($_SESSION['username'])) {
    header("Location: Login.php");
    exit;
}

// Pagination setup
$limit = 6; // maksimal mobil per halaman
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Hitung total mobil untuk pagination
$resultTotal = mysqli_query($conn, "SELECT COUNT(*) AS total FROM mobil");
$rowTotal = mysqli_fetch_assoc($resultTotal);
$totalMobil = $rowTotal['total'];
$totalPages = ceil($totalMobil / $limit);

// Ambil data mobil sesuai halaman
$query = mysqli_query($conn, "SELECT * FROM mobil LIMIT $limit OFFSET $offset");

if (!$query) {
    die("Query error: " . mysqli_error($conn));
}

// Hapus mobil jika request POST dari ajax
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    $stmt = mysqli_prepare($conn, "DELETE FROM mobil WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);

    if (mysqli_stmt_execute($stmt)) {
        echo 'success';
    } else {
        echo 'fail';
    }
    exit; // penting supaya tidak lanjut ke output HTML
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>JualMobil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-page">
    <?php include('Layouts/navbar.php'); ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">
                Selamat datang, <?= htmlspecialchars($_SESSION['username']) ?>!
            </h2>
            <p class="text-lg text-gray-600">
                Anda login sebagai <span class="font-semibold text-blue-600"><?= htmlspecialchars($_SESSION['role']) ?></span>
            </p>
        </div>

        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Katalog Mobil</h1>
            <?php if ($_SESSION['role'] === 'Super Admin'): ?>
                <a href="./SuperAdmin/TambahMobil.php" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i> Tambah Mobil
                </a>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php while($mobil = mysqli_fetch_assoc($query)): ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-transform duration-300 hover:-translate-y-1 hover:shadow-xl">
                    <div class="relative">
                        <img src="uploads/<?= htmlspecialchars($mobil['gambar']) ?>" 
                             alt="<?= htmlspecialchars($mobil['nama']) ?>" 
                             class="w-full h-56 object-cover" />
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 bg-blue-600 text-white rounded-full text-sm">
                                Stok: <?= $mobil['stok'] ?>
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">
                            <?= htmlspecialchars($mobil['nama']) ?>
                        </h2>
                        
                        <div class="mb-4">
                            <span class="text-2xl font-bold text-blue-600">
                                Rp <?= number_format($mobil['harga'], 0, ',', '.') ?>
                            </span>
                        </div>

                        <p class="text-gray-600 mb-6 line-clamp-3">
                            <?= htmlspecialchars($mobil['keterangan']) ?>
                        </p>

                        <div class="flex justify-between items-center">
                            <?php if ($_SESSION['role'] === 'Super Admin'): ?>
                                <div class="space-x-2">
                                    <a href="./SuperAdmin/EditMobil.php?id=<?= $mobil['id'] ?>" 
                                       class="inline-flex items-center px-3 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors duration-200">
                                        <i class="fas fa-edit mr-2"></i> Edit
                                    </a>
                                    <button onclick="hapusMobil(<?= $mobil['id'] ?>)" 
                                            class="inline-flex items-center px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-200">
                                        <i class="fas fa-trash mr-2"></i> Hapus
                                    </button>
                                </div>
                            <?php endif; ?>
                            <?php if ($_SESSION['role'] === 'Customer'): ?>
                                <a href="./Cust/Beli.php?id=<?= $mobil['id'] ?>" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                    <i class="fas fa-shopping-cart mr-2"></i> Pesan
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="mt-12 flex justify-center items-center gap-2">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    <i class="fas fa-chevron-left mr-2"></i> Prev
                </a>
            <?php endif; ?>

            <div class="flex gap-1">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>" 
                       class="px-4 py-2 rounded-lg transition-colors duration-200 <?= ($i == $page) 
                           ? 'bg-blue-600 text-white' 
                           : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    Next <i class="fas fa-chevron-right ml-2"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function hapusMobil(id) {
            if (confirm("Yakin ingin menghapus mobil ini?")) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "<?= $_SERVER['PHP_SELF'] ?>", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function () {
                    if (xhr.status == 200) {
                        if (xhr.responseText.trim() === 'success') {
                            alert('Mobil berhasil dihapus');
                            location.reload();
                        } else {
                            alert('Gagal menghapus mobil');
                        }
                    }
                };
                xhr.send("id=" + id);
            }
        }
    </script>
</body>
</html>
