<?php
include 'DB.php';

$success = "";
$error = "";
$fotoName = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $role = 'Customer';

    // Upload foto jika ada
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $fotoName = uniqid() . "_" . basename($_FILES["foto"]["name"]);
        $targetDir = "Uploads/";
        $targetFile = $targetDir . $fotoName;
        move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile);
    }

    // Cek username
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Username sudah terdaftar!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, email, password, role, foto) 
                  VALUES ('$username', '$email', '$hashed_password', '$role', '$fotoName')";
        if (mysqli_query($conn, $query)) {
            $success = "<script>alert('Registrasi berhasil! Silakan login.');window.location.href = 'Login.php';</script>";
        } else {
            $error = "Registrasi gagal: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - JualMobil</title>
    <link rel="stylesheet" href="./Style/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="auth-page">
    <div class="auth-container">
        <h2 style="padding-bottom: 1.5vh;">Create Account</h2>

        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="profile-photo-container">
                <img src="Uploads/Foto/Default.png" alt="Foto Profil" class="profile-photo-img" id="photo-preview">
                <label for="foto" class="edit-photo-btn">Pilih Foto Profil</label>
                <input type="file" name="foto" id="foto" accept="image/*" style="display: none;">
            </div>

            <div id="foto-status" class="form-group">
                <p style="color:#666; text-align: center; font-size: 0.9rem;">
                    ✱ Belum ada foto profil terpilih
                </p>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Register</button>

            <div class="auth-links">
                <p>Sudah punya akun? <a href="Login.php">Login Sekarang</a></p>
            </div>
        </form>
    </div>

    <script>
    function previewPhoto(event) {
        const reader = new FileReader();
        reader.onload = function(){
            document.getElementById("photo-preview").src = reader.result;
            document.getElementById("foto-status").innerHTML = 
                '<p style="color:#2e7d32; text-align: center; font-size: 0.9rem;">✓ Foto profil siap diupload</p>';
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    document.getElementById('foto').addEventListener('change', previewPhoto);
    </script>
</body>
</html>
