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
    <title>Register</title>
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
        justify-content: center;
        margin-bottom: 20px;
    }

    .profile-photo-img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #ccc;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }

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

    #foto {
        display: none;
    }
    </style>
</head>
<body>
    <h2>Halaman Register</h2>

    <?php if ($success): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php elseif ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <!-- FOTO -->
        <div class="profile-photo-container">
            <img src="Uploads/Foto/Default.png" alt="Foto Profil" class="profile-photo-img" id="photo-preview">
            <label for="foto" class="edit-photo-btn">Tambah Foto</label>
        </div>
        <input type="file" name="foto" id="foto" accept="image/*" onchange="previewPhoto(event)">

        <!-- STATUS FOTO -->
        <div id="foto-status">
            <p style="color:red;">✱ Anda belum meng‑upload foto profil.</p>
        </div>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Register</button>
    </form>

    <p>Sudah punya akun? <a href="Login.php">Login di sini</a></p>

    <script>
    function previewPhoto(event) {
        const reader = new FileReader();
        reader.onload = function(){
            document.getElementById("photo-preview").src = reader.result;
            document.getElementById("foto-status").innerHTML = '<p style="color:green;">✱ Foto profil Anda sudah terpasang.</p>';
        };
        reader.readAsDataURL(event.target.files[0]);
    }
    </script>
</body>
</html>
