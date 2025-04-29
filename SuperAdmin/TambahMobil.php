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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $stok = $_POST['stok'];
    $harga = $_POST['harga'];
    $keterangan = $_POST['keterangan'];

    // Upload Gambar
    $gambarName = $_FILES['gambar']['name'];
    $gambarTmp = $_FILES['gambar']['tmp_name'];
    $gambarPath = '../uploads/' . $gambarName;

    if (move_uploaded_file($gambarTmp, $gambarPath)) {
        $query = "INSERT INTO mobil (nama, stok, harga, keterangan, gambar) 
                  VALUES ('$nama', '$stok', '$harga', '$keterangan', '$gambarName')";
        $insert = mysqli_query($conn, $query);

        if ($insert) {
            $alert = "success";
        } else {
            $alert = "fail";
        }
    } else {
        $alert = "fail";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Tambah Mobil</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
  <div class="max-w-xl mx-auto bg-white shadow-xl rounded-lg p-6">
    <h1 class="text-2xl font-bold mb-4">Tambah Mobil</h1>

    <!-- Preview Gambar (Landscape 4:3) -->
    <div class="mb-4">
      <img id="preview" src="https://via.placeholder.com/640x360?text=Preview+Gambar+Mobil" class="w-full h-48 object-cover rounded border" />
    </div>

    <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
      <!-- Upload Gambar -->
      <div>
        <label class="block font-semibold mb-1">Gambar Mobil</label>
        <input type="file" name="gambar" accept="image/*" onchange="previewImage(event)" required
               class="border border-gray-300 p-2 w-full rounded">
      </div>

      <!-- Nama -->
      <div>
        <label class="block font-semibold mb-1">Nama Mobil</label>
        <input type="text" name="nama" class="border border-gray-300 p-2 w-full rounded" required>
      </div>

      <!-- Stok -->
      <div>
        <label class="block font-semibold mb-1">Stok</label>
        <input type="number" name="stok" class="border border-gray-300 p-2 w-full rounded" required>
      </div>

      <!-- Harga -->
      <div>
        <label class="block font-semibold mb-1">Harga</label>
        <input type="number" name="harga" class="border border-gray-300 p-2 w-full rounded" required>
      </div>

      <!-- Keterangan -->
      <div>
        <label class="block font-semibold mb-1">Keterangan</label>
        <textarea name="keterangan" rows="4" class="border border-gray-300 p-2 w-full rounded" required></textarea>
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
      alert("✅ Berhasil tambah mobil!");
      window.location.href = "../Index.php";
    <?php elseif ($alert === "fail"): ?>
      alert("❌ Gagal tambah mobil!");
    <?php endif; ?>
  </script>
</body>
</html>
