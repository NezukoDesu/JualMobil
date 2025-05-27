<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Super Admin') {
    echo "<script>
        alert('❌ Anda tidak bisa mengakses halaman ini!');
        window.location.href = '../Index.php';
    </script>";
    exit;
}

include '../DB.php';

$alert = "";

// Ambil ID mobil dari URL
$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id) {
    // Ambil data mobil berdasarkan ID
    $query = "SELECT * FROM mobil WHERE id = $id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $mobil = mysqli_fetch_assoc($result);
    } else {
        echo "<script>
            alert('❌ Mobil tidak ditemukan!');
            window.location.href = '../Index.php';
        </script>";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama = $_POST['nama'];
        $stok = $_POST['stok'];
        $harga = $_POST['harga'];
        $keterangan = $_POST['keterangan'];
        $gambarName = $_FILES['gambar']['name'];

        // Cek jika ada gambar baru yang diupload
        if ($gambarName) {
            $gambarTmp = $_FILES['gambar']['tmp_name'];
            $gambarPath = '../uploads/' . $gambarName;

            if (move_uploaded_file($gambarTmp, $gambarPath)) {
                // Update data dengan gambar baru
                $query = "UPDATE mobil SET nama = '$nama', stok = '$stok', harga = '$harga', keterangan = '$keterangan', gambar = '$gambarName' WHERE id = $id";
            } else {
                $alert = "fail";
            }
        } else {
            // Update tanpa mengganti gambar
            $query = "UPDATE mobil SET nama = '$nama', stok = '$stok', harga = '$harga', keterangan = '$keterangan' WHERE id = $id";
        }

        $update = mysqli_query($conn, $query);

        if ($update) {
            $alert = "success";
        } else {
            $alert = "fail";
        }
    }
} else {
    echo "<script>
        alert('❌ ID mobil tidak ditemukan!');
        window.location.href = '../Index.php';
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Mobil</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
  <div class="max-w-xl mx-auto bg-white shadow-xl rounded-lg p-6">
    <h1 class="text-2xl font-bold mb-4">Edit Mobil</h1>

    <!-- Preview Gambar (Landscape 4:3) -->
    <div class="mb-4">
      <img id="preview" src="../uploads/<?= htmlspecialchars($mobil['gambar']) ?>" class="w-full h-48 object-cover rounded border" />
    </div>

    <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
      <!-- Upload Gambar -->
      <div>
        <label class="block font-semibold mb-1">Gambar Mobil</label>
        <input type="file" name="gambar" accept="image/*" onchange="previewImage(event)"
               class="border border-gray-300 p-2 w-full rounded">
      </div>

      <!-- Nama -->
      <div>
        <label class="block font-semibold mb-1">Nama Mobil</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($mobil['nama']) ?>" class="border border-gray-300 p-2 w-full rounded" required>
      </div>

      <!-- Stok -->
      <div>
        <label class="block font-semibold mb-1">Stok</label>
        <input type="number" name="stok" value="<?= htmlspecialchars($mobil['stok']) ?>" class="border border-gray-300 p-2 w-full rounded" required>
      </div>

      <!-- Harga -->
      <div>
        <label class="block font-semibold mb-1">Harga</label>
        <input type="number" name="harga" value="<?= htmlspecialchars($mobil['harga']) ?>" class="border border-gray-300 p-2 w-full rounded" required>
      </div>

      <!-- Keterangan -->
      <div>
        <label class="block font-semibold mb-1">Keterangan</label>
        <textarea name="keterangan" rows="4" class="border border-gray-300 p-2 w-full rounded" required minlength="200"><?= htmlspecialchars($mobil['keterangan']) ?></textarea>
      </div>

      <!-- Tombol Simpan -->
      <div class="flex justify-between items-center">
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
          Simpan
        </button>
        <a href="../Index.php" class="text-red-600 hover:underline">← Kembali</a>
      </div>
    </form>
  </div>

  <script>
    function previewImage(event) {
      const reader = new FileReader();
      reader.onload = function(){
        const output = document.getElementById('preview');
        output.src = reader.result;
      };
      reader.readAsDataURL(event.target.files[0]);
    }

    <?php if ($alert === "success"): ?>
      alert("✅ Berhasil edit mobil!");
      window.location.href = "../Index.php";
    <?php elseif ($alert === "fail"): ?>
      alert("❌ Gagal edit mobil!");
    <?php endif; ?>
  </script>
</body>
</html>
