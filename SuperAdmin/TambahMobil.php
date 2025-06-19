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
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $stok = (int)$_POST['stok'];
    $harga = (int)$_POST['harga'];
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    // Upload Gambar
    $uploadDir = "../uploads/";
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $gambarName = uniqid() . '_' . basename($_FILES['gambar']['name']);
        $gambarPath = $uploadDir . $gambarName;
        
        // Verify file type
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($gambarPath, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed) && move_uploaded_file($_FILES['gambar']['tmp_name'], $gambarPath)) {
            $query = "INSERT INTO mobil (nama, stok, harga, keterangan, gambar) 
                      VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'siiss', $nama, $stok, $harga, $keterangan, $gambarName);
            
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>
                    alert('✅ Berhasil menambah mobil!');
                    window.location.href = '../Index.php';
                </script>";
                exit;
            } else {
                $alert = "fail";
            }
        } else {
            echo "<script>alert('❌ Tipe file tidak diizinkan! Gunakan JPG, JPEG, PNG, atau WEBP.');</script>";
        }
    } else {
        echo "<script>alert('❌ Harap pilih gambar mobil!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Mobil - JualMobil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 admin-page">
    <?php include('../Layouts/navbar.php'); ?>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-xl p-8">
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-gray-800">Tambah Mobil Baru</h1>
                <p class="text-gray-600 mt-2">Lengkapi informasi mobil yang akan ditambahkan</p>
            </div>

            <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                <!-- Image Preview -->
                <div class="relative group">
                    <div class="aspect-video rounded-lg overflow-hidden bg-gray-100">
                        <img id="preview" src="https://via.placeholder.com/1280x720?text=Upload+Foto+Mobil" 
                             class="w-full h-full object-cover transition duration-300 group-hover:opacity-75" />
                    </div>
                    <label for="gambar" class="absolute bottom-4 right-4 bg-blue-600 text-white px-4 py-2 rounded-lg cursor-pointer hover:bg-blue-700 transition">
                        <i class="fas fa-camera mr-2"></i>Pilih Foto
                    </label>
                    <input type="file" id="gambar" name="gambar" accept="image/*" class="hidden" required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Mobil</label>
                        <input type="text" name="nama" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                            <input type="number" name="harga" required
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stok</label>
                        <input type="number" name="stok" required min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="5" required minlength="200"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Minimal 200 karakter..."></textarea>
                </div>

                <div class="flex justify-end space-x-4 pt-4">
                    <a href="../Index.php" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-save mr-2"></i>Simpan
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
    </script>
</body>
</html>
