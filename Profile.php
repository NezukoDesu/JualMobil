<?php
session_start();
include('DB.php');

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: Login.php");
    exit;
}

$username = $_SESSION['username'];

// Ambil data user
$query = "SELECT * FROM users WHERE username = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Proses update
$success = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_username = $_SESSION['username'];
    $new_username = $_POST['username'];
    $email = $_POST['email'];
    $new_password = $_POST['password'];
    $foto = $user['foto']; // Default foto lama

    // Cek upload foto baru
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $fotoName = uniqid() . "." . $ext;
        $targetFile = $targetDir . $fotoName;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetFile)) {
            $foto = $fotoName;
        }
    }

    // Mulai transaksi
    mysqli_begin_transaction($conn);
    try {
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $query = "UPDATE users SET username=?, email=?, password=?, foto=? WHERE username=?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'sssss', $new_username, $email, $hashed_password, $foto, $old_username);
        } else {
            $query = "UPDATE users SET username=?, email=?, foto=? WHERE username=?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'ssss', $new_username, $email, $foto, $old_username);
        }

        if (mysqli_stmt_execute($stmt)) {
            // Update session with username baru
            $_SESSION['username'] = $new_username;
            $_SESSION['foto'] = $foto; // Add this line to update photo in session
            
            // Ambil data terbaru
            $query = "SELECT * FROM users WHERE username = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 's', $new_username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
            
            mysqli_commit($conn);
            $success = "Profil berhasil diperbarui.";
        } else {
            throw new Exception("Gagal memperbarui profil.");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $success = "Gagal memperbarui profil: " . $e->getMessage();
    }
}

// Setelah update atau jika tidak ada update, ambil data user terbaru
if (!isset($user) || $user === null) {
    $current_username = $_SESSION['username'];
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $current_username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Saya - JualMobil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 admin-page">
    <?php include('Layouts/navbar.php'); ?>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white/90 backdrop-blur-md rounded-lg shadow-xl p-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800">Profil Saya</h2>
                <p class="text-gray-600 mt-2">Kelola informasi profil Anda</p>
            </div>

            <?php if ($success): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <!-- Foto Profile -->
                <div class="flex flex-col items-center space-y-4">
                    <?php
                    $fotoPath = !empty($user['foto']) && file_exists('Uploads/' . $user['foto'])
                        ? 'Uploads/' . htmlspecialchars($user['foto'])
                        : 'Uploads/Foto/Default.png';
                    ?>
                    <div class="relative">
                        <img src="<?php echo $fotoPath; ?>"
                             alt="Foto Profil"
                             class="w-32 h-32 rounded-full object-cover border-4 border-blue-200"
                             id="photo-preview">
                        <label for="foto" 
                               class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full cursor-pointer hover:bg-blue-700 transition">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" name="foto" id="foto" accept="image/*" class="hidden">
                    </div>
                    <div id="foto-status" class="text-sm"></div>
                </div>

                <!-- Form Fields -->
                <div class="grid gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" name="username"
                               value="<?php echo htmlspecialchars($user['username']); ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email"
                               value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Password Baru <span class="text-gray-500">(kosongkan jika tidak ingin ganti)</span>
                        </label>
                        <input type="password" name="password"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-4 pt-4">
                    <a href="Index.php" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Kembali
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    const fotoInput = document.getElementById('foto');
    const fotoStatus = document.getElementById('foto-status');
    const previewImg = document.getElementById('photo-preview');

    fotoInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const fileName = this.files[0].name;
            fotoStatus.innerHTML = `
                <div class="text-blue-600">
                    <i class="fas fa-check-circle"></i> Foto baru dipilih: ${fileName}
                </div>`;
            const reader = new FileReader();
            reader.onload = e => previewImg.src = e.target.result;
            reader.readAsDataURL(this.files[0]);
        }
    });
    </script>
</body>
</html>


