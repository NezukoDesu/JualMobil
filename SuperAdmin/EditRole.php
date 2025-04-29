<?php
session_start();
include('../DB.php');

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

// Hanya Super Admin dan Manager yang boleh akses
if ($_SESSION['role'] !== 'Super Admin' && $_SESSION['role'] !== 'Manager') {
    header("Location: ../Index.php");
    exit;
}

// Cek apakah ada ID pengguna yang akan diedit
if (!isset($_GET['id'])) {
    echo "Tidak ada ID yang dipilih!";
    exit;
}

$edit_id = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil data user yang akan diedit berdasarkan ID
$query = "SELECT * FROM users WHERE id = '$edit_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo "Pengguna tidak ditemukan!";
    exit;
}

// Jika user yang login adalah Manager, larang akses ke Super Admin
if ($_SESSION['role'] === 'Manager' && $user['role'] === 'Super Admin') {
    echo '
    <script>
        alert("Akses Ditolak! Anda tidak memiliki izin untuk mengubah data Super Admin.");
        window.location.href = "DataAdmin.php";
    </script>';
    exit;
}

// Proses update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = mysqli_real_escape_string($conn, $_POST['username']);
    $new_role = mysqli_real_escape_string($conn, $_POST['role']);

    // Jika Manager mencoba ubah ke Super Admin, tolak
    if ($_SESSION['role'] === 'Manager' && $new_role === 'Super Admin') {
        $error = "Manager tidak diizinkan mengubah role menjadi Super Admin!";
    } else {
        $update_query = "UPDATE users SET username = '$new_username', role = '$new_role' WHERE id = '$edit_id'";
        if (mysqli_query($conn, $update_query)) {
            header("Location: DataAdmin.php");
            exit;
        } else {
            $error = "Gagal memperbarui data!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Role</title>
    <link rel="stylesheet" href="../Style/style.css">
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f7fa;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 500px;
        margin: 50px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
    }

    label {
        font-size: 16px;
        color: #555;
        margin-bottom: 5px;
        display: inline-block;
    }

    input[type="text"], select {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
        background-color: #f9f9f9;
        transition: border-color 0.3s ease;
    }

    input[type="text"]:focus, select:focus {
        border-color: #007BFF;
        outline: none;
    }

    button[type="submit"] {
        width: 100%;
        padding: 12px;
        background-color: #007BFF;
        color: #fff;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button[type="submit"]:hover {
        background-color: #0056b3;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .profile-info {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .profile-info p {
        margin: 5px 0;
        font-size: 16px;
        color: #555;
    }

    .profile-info img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
    }
    </style>
</head>
<body>

<h2>Edit Role: <?php echo htmlspecialchars($user['username']); ?></h2>

<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<div class="container">
    <h3>Profil Pengguna</h3>
    <div class="profile-info" style="text-align: center; background-color: #FFFFFF;">
        <!-- Menampilkan Gambar Profil -->
        <?php if (!empty($user['foto'])): ?>
            <img  src="../uploads/<?php echo htmlspecialchars($user['foto']); ?>" alt="Profil Gambar">
        <?php else: ?>
            <img src="../uploads/Foto/Default.png" alt="Profil Gambar">
        <?php endif; ?>
    </div>

    <h2>Update User Role</h2>
    <form action="" method="POST">
        <div class="form-group">
            <label>Username:</label><br>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required readonly><br><br>
            <label>Email:</label><br>
            <input type="text" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required readonly><br>
        </div>

        <div class="form-group">
            <label>Role:</label><br>
            <select name="role" required>
                <?php if ($_SESSION['role'] === 'Super Admin'): ?>
                    <option value="Super Admin" <?php if ($user['role'] == 'Super Admin') echo 'selected'; ?>>Super Admin</option>
                <?php endif; ?>
                <option value="Manager" <?php if ($user['role'] == 'Manager') echo 'selected'; ?>>Manager</option>
                <option value="Sales" <?php if ($user['role'] == 'Sales') echo 'selected'; ?>>Sales</option>
                <option value="Customer" <?php if ($user['role'] == 'Customer') echo 'selected'; ?>>Customer</option>
            </select><br><br>
        </div>

        <div class="form-group">
            <button type="submit">Update Role</button><br><br>
            <button type="button" onclick="window.location.href='DataAdmin.php'" style="width: 100px; margin-left:300px;">Kembali</button>
        </div>
    </form>
</div>

</body>
</html>
