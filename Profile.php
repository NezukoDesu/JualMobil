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
    $username = $_POST['username'];
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

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET username=?, email=?, password=?, foto=? WHERE username=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'sssss', $username, $email, $hashed_password, $foto, $username);
    } else {
        $query = "UPDATE users SET username=?, email=?, foto=? WHERE username=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'ssss', $username, $email, $foto, $username);
    }

    if (mysqli_stmt_execute($stmt)) {
        $success = "Profil berhasil diperbarui.";
        // Ambil data terbaru
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
    } else {
        $success = "Gagal memperbarui profil.";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Saya</title>
    <link rel="stylesheet" href="./Style/style.css">
    <style>
        .profile-container {
    display: flex;
    align-items: center;
    gap: 20px;
}

.profile-img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    border: 3px solid #ccc;
}

.profile-photo-container {
  display: flex;
  align-items: center;
  justify-content: center;  /* center secara horizontal */
  margin-bottom: 20px;
}

/* Gaya foto profil bulat */
.profile-photo-img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #ccc;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

/* Tombol edit foto */
.edit-photo-btn {
  margin-left: 15px;
  padding: 8px 14px;
  font-size: 14px;
  background-color: #007bff;
  color: #fff;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color .2s;
}
.edit-photo-btn:hover {
  background-color: #0056b3;
}

/* Sembunyikan input file asli */
#foto {
  display: none;
}

    </style>
</head>
<body>
  <h2>Profil Saya</h2>
  <p>Selamat datang, <?php echo htmlspecialchars($user['username']); ?>!</p>
  <?php if ($success): ?>
    <p style="color: green;"><?php echo $success; ?></p>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <?php
      // Tentukan path foto (DB vs default)
      $fotoPath = !empty($user['foto']) && file_exists('Uploads/' . $user['foto'])
        ? 'Uploads/' . htmlspecialchars($user['foto'])
        : 'Uploads/Foto/Default.png';
    ?>

    <!-- STATUS FOTO -->
    <div id="foto-status">
      <?php if (!empty($user['foto']) && file_exists('Uploads/' . $user['foto'])): ?>
        <p style="color:green;">✱ Foto profil Anda sudah terpasang.</p>
      <?php else: ?>
        <p style="color:red;">✱ Anda belum meng‑upload foto profil.</p>
      <?php endif; ?>
    </div>

    <!-- FOTO & PREVIEW -->
    <div class="profile-photo-container">
      <img src="<?php echo $fotoPath; ?>"
           alt="Foto Profil"
           class="profile-photo-img" id="photo-preview">
      <label for="foto" class="edit-photo-btn">Edit Foto</label>
    </div>
    <input type="file" name="foto" id="foto" accept="image/*">

    <!-- Field lainnya -->
    <label>Nama Pengguna:</label><br>
    <input type="text" name="username"
           value="<?php echo htmlspecialchars($user['username']); ?>"
           required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email"
           value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
           required><br><br>

    <label>Password Baru (kosongkan jika tidak ingin ganti):</label><br>
    <input type="password" name="password"><br><br>

    <button type="submit">Simpan Perubahan</button>
    <br><br>
    <button type="button" onclick="window.location.href='Index.php';" style="width:200px; margin-left:200px;" class="bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700">← Kembali ke Dashboard</button>

  </form>

  <br>

  <script>
    const fotoInput = document.getElementById('foto');
    const fotoStatus = document.getElementById('foto-status');
    const previewImg = document.getElementById('photo-preview');

    fotoInput.addEventListener('change', function() {
      if (this.files && this.files[0]) {
        const fileName = this.files[0].name;
        // Update status
        fotoStatus.innerHTML = `<p style="color:blue;">✱ File siap di‑upload: ${fileName}</p>`;
        // Tampilkan preview
        const reader = new FileReader();
        reader.onload = e => previewImg.src = e.target.result;
        reader.readAsDataURL(this.files[0]);
      } else {
        // Kembalikan status awal
        <?php if (!empty($user['foto']) && file_exists('Uploads/' . $user['foto'])): ?>
          fotoStatus.innerHTML = `<p style="color:green;">✱ Foto profil Anda sudah terpasang.</p>`;
        <?php else: ?>
          fotoStatus.innerHTML = `<p style="color:red;">✱ Anda belum meng‑upload foto profil.</p>`;
        <?php endif; ?>
      }
    });
  </script>
</body>
</html>


