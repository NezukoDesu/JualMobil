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
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $stok = (int)$_POST['stok'];
        $harga = (int)$_POST['harga'];
        $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
        $gambar = $mobil['gambar']; // Default to existing image

        // Handle image upload
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = "../uploads/";
            
            // Create directory if doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $fileName = $_FILES['gambar']['name'];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (in_array($fileExt, $allowed)) {
                $newFileName = uniqid() . '.' . $fileExt;
                $uploadPath = $uploadDir . $newFileName;

                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadPath)) {
                    // Delete old image if exists
                    if ($mobil['gambar'] && file_exists($uploadDir . $mobil['gambar'])) {
                        unlink($uploadDir . $mobil['gambar']);
                    }
                    $gambar = $newFileName;
                }
            }
        }

        // Update database
        $query = "UPDATE mobil SET nama=?, stok=?, harga=?, keterangan=?, gambar=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'siissi', $nama, $stok, $harga, $keterangan, $gambar, $id);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
                alert('✅ Berhasil mengupdate mobil!');
                window.location.href = '../Index.php';
            </script>";
            exit;
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
    <title>Edit Mobil - JualMobil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 admin-page">
    <?php include('../Layouts/navbar.php'); ?>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-xl p-8">
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-gray-800">Edit Mobil</h1>
                <p class="text-gray-600 mt-2">Edit informasi mobil <?= htmlspecialchars($mobil['nama']) ?></p>
            </div>

            <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                <!-- Image Preview -->
                <div class="relative group">
                    <div class="aspect-video rounded-lg overflow-hidden bg-gray-100">
                        <img id="preview" src="../uploads/<?= htmlspecialchars($mobil['gambar']) ?>" 
                             class="w-full h-full object-cover transition duration-300 group-hover:opacity-75" />
                    </div>
                    <label for="gambar" class="absolute bottom-4 right-4 bg-blue-600 text-white px-4 py-2 rounded-lg cursor-pointer hover:bg-blue-700 transition">
                        <i class="fas fa-camera mr-2"></i>Ganti Foto
                    </label>
                    <input type="file" id="gambar" name="gambar" accept="image/*" class="hidden">
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
                <div class="flex justify-end space-x-4 pt-4">
                    <a href="../Index.php" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
document.getElementById('gambar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Verify file type
        const fileType = file.type;
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        
        if (!validTypes.includes(fileType)) {
            alert('❌ Tipe file tidak diizinkan! Gunakan JPG, JPEG, PNG, atau WEBP.');
            this.value = '';
            return;
        }

        // Size limit 5MB
        if (file.size > 5 * 1024 * 1024) {
            alert('❌ Ukuran file terlalu besar! Maksimal 5MB.');
            this.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function() {
            document.getElementById('preview').src = reader.result;
        }
        reader.readAsDataURL(file);
    }
});

<?php if ($alert === "success"): ?>
    alert("✅ Berhasil edit mobil!");
    window.location.href = "../Index.php";
<?php elseif ($alert === "fail"): ?>
    alert("❌ Gagal edit mobil!");
<?php endif; ?>
</script>
</body>
</html>
