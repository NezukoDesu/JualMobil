<?php 
session_start();
include 'DB.php';
?>


<?php 
if (!isset($_SESSION['username'])) {
    header("Location: Login.php");
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM mobil");

if (!$query) {
    die("Query error: " . mysqli_error($conn));
}

if (!isset($_SESSION['username'])) {
    echo 'unauthorized';
    exit;
}

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Query untuk menghapus data mobil berdasarkan ID
    $query = "DELETE FROM mobil WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);

    if (mysqli_stmt_execute($stmt)) {
        echo 'success';
    } else {
        echo 'fail';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>JualMobil</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <nav class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
      <div class="text-xl font-bold text-blue-600">JualMobil</div>
      <div class="hidden md:flex gap-6 items-center">
        <a href="Index.php" class="text-gray-700 hover:text-blue-500">Home</a>
        <?php
        if ($_SESSION['role'] === 'Super Admin' ) {
        echo '<a href="./SuperAdmin/EditMobil.php" class="text-gray-700 hover:text-blue-500">Data Mobil</a>';
        }
        if ($_SESSION['role'] === 'Super Admin' || $_SESSION['role'] === 'Manager') {
            echo '<a href="./SuperAdmin/DataAdmin.php" class="text-gray-700 hover:text-blue-500">Data Admin</a>';
        }
        if ($_SESSION['role'] === 'Super Admin' || $_SESSION['role'] === 'Manager') {
            echo '<a href="./SuperAdmin/Laporan.php" class="text-gray-700 hover:text-blue-500">Data Laporan</a>';
        }
        if ($_SESSION['role'] === 'Manager' ) {
            echo '<a href="./Manager/Chat.php" class="text-gray-700 hover:text-blue-500">Contact Sales</a>';
        }
        if ($_SESSION['role'] === 'Sales' ) {
            echo '<a href="./Sales/Pesanan.php" class="text-gray-700 hover:text-blue-500">Buat Pesanan</a>';
        }
        if ($_SESSION['role'] === 'Sales' ) {
            echo '<a href="./Sales/Chat.php" class="text-gray-700 hover:text-blue-500">Chat</a>';
        }
        if ($_SESSION['role'] === 'Customer' ) {
            echo '<a href="./Cust/Request.php" class="text-gray-700 hover:text-blue-500">Pesanan</a>';
        }
        if ($_SESSION['role'] === 'Customer' ) {
            echo '<a href="./Cust/Chat.php" class="text-gray-700 hover:text-blue-500">Contact Sales</a>';
        }
        ?>
        <?php
            if (isset($_SESSION['username'])) {
                $username = $_SESSION['username'];
                $stmt = mysqli_prepare($conn, "SELECT foto FROM users WHERE username = ?");
                mysqli_stmt_bind_param($stmt, 's', $username);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($res);
                $userFoto = $row['foto'] ?? '';
            } else {
                $userFoto = '';
            }

                // Tentukan path foto (upload atau default)
            $fotoPath = $userFoto && file_exists('Uploads/' . $userFoto)
                ? 'Uploads/' . htmlspecialchars($userFoto)
                : 'Uploads/Foto/Default.png';
        ?>
        <a href="Profile.php" class="block">
            <img
                src="<?php echo $fotoPath; ?>"
                alt="Foto Profil"
                title="Profil Saya"
                class="w-8 h-8 rounded-full object-cover border-2 border-gray-300 hover:border-red-500 transition-all"/>
        </a>
        
        <a href="Logout.php">
            <button class="bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700">Logout</button>
        </a>
        </div>
    </div>
  </nav>

  <!-- <script>
    const btn = document.getElementById("menu-btn");
    const menu = document.getElementById("mobile-menu");

    btn.addEventListener("click", () => {
      menu.classList.toggle("hidden");
    });
  </script> -->

  <h2 class="text-2xl md:text-3xl font-bold text-blue-700 text-center mt-8 mb-2">
  Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!
</h2>
<p class="text-md text-gray-600 text-center mb-8">
  Anda login sebagai <span class="font-semibold text-blue-500"><?php echo htmlspecialchars($_SESSION['role']); ?></span>
</p>

    <div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold">Daftar Mobil</h1>
    <?php 
    if ($_SESSION['role'] === 'Super Admin' ) {
        echo '<a href="./SuperAdmin/TambahMobil.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Tambah Mobil</a>';
        }
    ?>
      
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php while($mobil = mysqli_fetch_assoc($query)): ?>
        <div class="bg-white rounded shadow p-4 relative">
          <!-- Gambar -->
          <img src="uploads/<?= htmlspecialchars($mobil['gambar']) ?>" alt="<?= htmlspecialchars($mobil['nama']) ?>" class="w-full h-48 object-cover rounded mb-4">

          <!-- Info Mobil -->
          <h2 class="text-xl font-semibold"><?= htmlspecialchars($mobil['nama']) ?></h2>
          <p class="text-sm text-gray-600 mb-1">Stok: <?= $mobil['stok'] ?></p>
          <p class="text-sm text-gray-600 mb-1">Harga: Rp <?= number_format($mobil['harga'], 0, ',', '.') ?></p>
          <p class="text-sm text-gray-600 mb-4"><?= htmlspecialchars($mobil['keterangan']) ?></p>

          <!-- Tombol Edit & Hapus -->
          <div class="flex justify-between">
        <?php 
            if ($_SESSION['role'] === 'Super Admin' ) {
                echo '<a href="./SuperAdmin/EditMobil.php?id=' . $mobil['id'] . '" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Edit</a>';
                echo '<a href="javascript:void(0)" onclick="hapusMobil(' . $mobil['id'] . ')" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Hapus</a>';
            }
            if ($_SESSION['role'] === 'Customer' ) {
                echo '<a href="./Cust/Beli.php?id=' . $mobil['id'] . '" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Pesan</a>';
            }
        ?>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>

  <script>
    function hapusMobil(id) {
    if (confirm("Yakin ingin menghapus mobil ini?")) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "SuperAdmin/HapusMobil.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function () {
            if (xhr.status == 200) {
                if (xhr.responseText == 'success') {
                    alert('Mobil berhasil dihapus');
                    location.reload();  // Memuat ulang halaman untuk memperbarui tampilan
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
