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
</head>
<body class="bg-gray-100">
  <nav class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
      <div class="text-xl font-bold text-blue-600">JualMobil</div>
      <div class="hidden md:flex gap-6 items-center">
        <a href="Index.php" class="text-gray-700 hover:text-blue-500">Home</a>
        <?php if ($_SESSION['role'] === 'Super Admin'): ?>
            <a href="./Manager/DataMobil.php" class="text-gray-700 hover:text-blue-500">Data Mobil</a>
        <?php endif; ?>
        <?php if ($_SESSION['role'] === 'Super Admin' || $_SESSION['role'] === 'Manager'): ?>
            <a href="./SuperAdmin/DataAdmin.php" class="text-gray-700 hover:text-blue-500">Data Admin</a>
            <a href="./SuperAdmin/Laporan.php" class="text-gray-700 hover:text-blue-500">Data Laporan</a>
        <?php endif; ?>
        <?php if ($_SESSION['role'] === 'Manager'): ?>
            <a href="./Manager/Chat.php" class="text-gray-700 hover:text-blue-500">Contact Sales</a>
        <?php endif; ?>
        <?php if ($_SESSION['role'] === 'Sales'): ?>
            <a href="./Sales/Chat.php" class="text-gray-700 hover:text-blue-500">Chat</a>
        <?php endif; ?>
        <?php if ($_SESSION['role'] === 'Customer'): ?>
            <a href="./Cust/Request.php" class="text-gray-700 hover:text-blue-500">Pesanan</a>
            <a href="./Cust/Chat.php" class="text-gray-700 hover:text-blue-500">Contact Sales</a>
        <?php endif; ?>

        <?php
        // Ambil foto profil user
        $userFoto = '';
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
            $stmt = mysqli_prepare($conn, "SELECT foto FROM users WHERE username = ?");
            mysqli_stmt_bind_param($stmt, 's', $username);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($res);
            $userFoto = $row['foto'] ?? '';
        }
        $fotoPath = $userFoto && file_exists('Uploads/' . $userFoto)
            ? 'Uploads/' . htmlspecialchars($userFoto)
            : 'Uploads/Foto/Default.png';
        ?>
        <a href="Profile.php" class="block">
          <img
            src="<?= $fotoPath ?>"
            alt="Foto Profil"
            title="Profil Saya"
            class="w-8 h-8 rounded-full object-cover border-2 border-gray-300 hover:border-red-500 transition-all"
          />
        </a>

        <a href="Logout.php">
          <button class="bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700">Logout</button>
        </a>
      </div>
    </div>
  </nav>

  <h2 class="text-2xl md:text-3xl font-bold text-blue-700 text-center mt-8 mb-2">
    Selamat datang, <?= htmlspecialchars($_SESSION['username']) ?>!
  </h2>
  <p class="text-md text-gray-600 text-center mb-8">
    Anda login sebagai <span class="font-semibold text-blue-500"><?= htmlspecialchars($_SESSION['role']) ?></span>
  </p>

  <div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold">Daftar Mobil</h1>
      <?php if ($_SESSION['role'] === 'Super Admin'): ?>
        <a href="./SuperAdmin/TambahMobil.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Tambah Mobil</a>
      <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php while($mobil = mysqli_fetch_assoc($query)): ?>
        <div class="bg-white rounded shadow p-4 relative">
          <img src="uploads/<?= htmlspecialchars($mobil['gambar']) ?>" alt="<?= htmlspecialchars($mobil['nama']) ?>" class="w-full h-48 object-cover rounded mb-4" />
          <h2 class="text-xl font-semibold"><?= htmlspecialchars($mobil['nama']) ?></h2>
          <p class="text-sm text-gray-600 mb-1">Stok: <?= $mobil['stok'] ?></p>
          <p class="text-sm text-gray-600 mb-1">Harga: Rp <?= number_format($mobil['harga'], 0, ',', '.') ?></p>
          <p class="text-sm text-gray-600 mb-4 max-h-20 overflow-y-auto"><?= htmlspecialchars($mobil['keterangan']) ?></p>

          <div class="flex justify-between">
            <?php if ($_SESSION['role'] === 'Super Admin'): ?>
              <a href="./SuperAdmin/EditMobil.php?id=<?= $mobil['id'] ?>" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Edit</a>
              <a href="javascript:void(0)" onclick="hapusMobil(<?= $mobil['id'] ?>)" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Hapus</a>
            <?php endif; ?>
            <?php if ($_SESSION['role'] === 'Customer'): ?>
              <a href="./Cust/Beli.php?id=<?= $mobil['id'] ?>" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Pesan</a>
            <?php endif; ?>
          </div>
        </div>
      <?php endwhile; ?>
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex justify-center gap-3" style="margin-bottom: 20px;">
      <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Prev</a>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>" class="px-3 py-1 rounded hover:bg-gray-300 <?= ($i == $page) ? 'bg-gray-300 font-bold' : 'bg-white' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>

      <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Next</a>
      <?php endif; ?>
    </div>
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
